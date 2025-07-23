<?php

namespace App\Services;

use App\Events\ImportFailed;
use App\Jobs\ImportDataChunkJob;
use App\Jobs\ImportDataJob;
use App\Models\AuditLog;
use App\Models\ErrorLog;
use App\Models\ImportLog;
use App\Models\Order;
use App\Models\Price;
use App\Models\PriceDiscount;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @class Import service
 */
class ImportService
{
    /**
     * Get import types
     *
     * @param bool $allTypes
     * @return array
     */
    public function getImportTypes(bool $allTypes = true): array
    {
        $config = config('imports');
        $available = [];

        if (!empty($config)) {
            foreach ($config as $key => $import) {
                $permission = $import['permission_required'] ?? null;

                if ($allTypes || (!$permission || Auth::user()->can($permission))) {
                    $available[$key] = [
                        'label' => $import['label'],
                        'permission_required' => $import['permission_required'],
                        'files' => collect($import['files'])->map(function ($file) {
                            return [
                                'label' => $file['label'],
                                'headers' => collect($file['headers_to_db'])->map(function ($col) {
                                    return [
                                        'label' => $col['label'],
                                        'type' => $col['type'],
                                        'validation' => collect($col['validation'] ?? [])
                                            ->map(function ($rule, $key) {
                                                if (is_int($key)) {
                                                    return $rule;
                                                }

                                                if (is_array($rule)) {
                                                    return $key . ':' . collect($rule)->map(fn($v, $k) => "$k=$v")->implode(',');
                                                }

                                                return $key . ':' . $rule;
                                            })
                                            ->values()
                                            ->toArray(),
                                    ];
                                })->toArray(),
                            ];
                        })->toArray(),
                    ];
                }
            }
        }
        return $available;
    }

    /**
     * Upload file
     *
     * @param $data
     * @return bool|string
     */
    public function upload($data)
    {
        $importType = $data['import_type'];
        $files = $data['files'];

        $config = $this->getImportConfig($importType);
        $filesConfig = $config['files'] ?? [];

        if (!empty($files)) {
            foreach ($files as $fileKey => $file) {
                if (!empty($filesConfig[$fileKey])) {
                    $headersConfig = $filesConfig[$fileKey]['headers_to_db'];
                    $fileLabel = $filesConfig[$fileKey]['label'];

                    $validationResult = $this->validateImportFile($file, $headersConfig, $fileLabel);
                    if ($validationResult !== true) {
                        // All files must be good to launch jobs
                        return $validationResult;
                    }
                }
            }

            foreach ($files as $fileKey => $file) {
                if (!empty($filesConfig[$fileKey])) {
                    $originalName = $file->getClientOriginalName();
                    $storedPath = $file->storeAs('imports/temp', Str::uuid() . '_' . $originalName);

                    $userId = Auth::id();
                    $importId = ImportLog::insertGetId([
                        'user_id'     => $userId,
                        'import_type' => $importType,
                        'file_key'    => $fileKey,
                        'file_name'   => $originalName,
                        'status'      => 'processing',
                        'created_at'  => Carbon::now()
                    ]);

                    ImportDataJob::dispatch($importType, $storedPath, $fileKey, $importId);
                }
            }
        }

        return 'Import is in progress. You will be notified when it is finished.';
    }

    /**
     * Validate import file
     *
     * @param $file
     * @param array $headersConfig
     * @param $fileLabel
     * @return bool|string
     */
    protected function validateImportFile($file, array $headersConfig, $fileLabel)
    {
        $sheetArray = Excel::toArray([], $file)[0] ?? [];
        if (empty($sheetArray)) {
            return "File is empty or cannot be read.";
        }

        $headings = $sheetArray[0];

        $configHeaders = array_keys($headersConfig);

        if (count($headings) !== count($configHeaders)) {
            return "Number of columns in file ($fileLabel) " . count($headings) . " does not match expected " . count($configHeaders) . ".";
        }

        if ($headings !== $configHeaders) {
            return "Headers in file ($fileLabel) do not match expected headers names.";
        }

        if (count($sheetArray) < 2) {
            return "File ($fileLabel) contains only headers but no data rows.";
        }

        return true;
    }

    /**
     * Process the import file and dispatch chunked jobs for processing its data.
     *
     * @param string $absolutePath
     * @param $importType
     * @param $fileKey
     * @param $importId
     * @return void
     * @throws \Throwable
     */
    public function processFile(string $absolutePath, $importType, $fileKey, $importId): void
    {
        $rows = Excel::toArray([], $absolutePath)[0] ?? [];

        $jobs = [];
        $iterator = 0;
        $chunkLength = 200;
        $chunks = array_chunk(array_slice($rows, 1), $chunkLength);
        if (!empty($chunks)) {
            foreach ($chunks as $chunk) {
                $chunkWithHeader = array_merge([$rows[0]], $chunk);

                $jobs[] = new ImportDataChunkJob(
                    $importType,
                    $chunkWithHeader,
                    $fileKey,
                    $importId,
                    $iterator
                );
                $iterator += $chunkLength;
            }
        }

        Bus::batch($jobs)
            ->then(function () use ($importId) {
                $import = ImportLog::find($importId);
                if (!empty($import)) {
                    $errorItems = ErrorLog::where('import_id', $importId)->get()->all();

                    $import->update([
                        'status' => !empty($errorItems) ? 'unsuccessful' : 'success'
                    ]);

                    // This can be also job to avoid sending multiple emails in short time
                    if (!empty($errorItems)) {
                        if (count($errorItems) > 500) {
                            foreach (array_chunk($errorItems, 500) as $chunk) {
                                event(new ImportFailed($import, $chunk));
                            }
                        } else {
                            event(new ImportFailed($import, $errorItems));
                        }
                    }
                }
            })
            ->catch(function () use ($importId) {
                $importLog = ImportLog::find($importId);
                if (!empty($importLog)) {
                    $importLog->update([
                        'status' => 'unsuccessful'
                    ]);
                }
            })
            ->dispatch();
    }

    /**
     * Process rows
     *
     * @param string $importType
     * @param array $rows
     * @param string $fileKey
     * @param int $importId
     * @param int $iterator
     * @return void
     */
    public function processRows(string $importType, array $rows, string $fileKey, int $importId, int $iterator): void
    {
        $fileConfig = $this->getFileImportConfiguration($importType, $fileKey);
        $model = $this->getDataTableModel($importType, $fileKey);

        $errorItems = [];
        $auditItems = [];

        if (!empty($rows)) {
            $headerFields = $fileConfig['headers_to_db'] ?? [];
            $updateOrCreateFields = $fileConfig['update_or_create'] ?? null;

            $headerRow = array_shift($rows);
            foreach ($rows as $row) {
                $iterator++;
                $data = [];
                $rules = [];

                $typeValidator = [];
                foreach ($headerRow as $i => $headerName) {
                    $fieldConfig = $headerFields[$headerName] ?? null;

                    $value = $row[$i] ?? null;

                    $typeMessage = !empty($fieldConfig) && !empty($value) ? $this->getDataTypeErrorMessage($fieldConfig, $value) : null;

                    $data[$headerName] = $value;
                    if (empty($typeMessage)) {
                        if (is_array($fieldConfig) && isset($fieldConfig['validation'])) {
                            foreach ($fieldConfig['validation'] as $key => $validationRule) {
                                if (is_int($key)) {
                                    $rules[$headerName][] = $validationRule;
                                } elseif ($key === 'exists') {
                                    $rules[$headerName][] = Rule::exists($validationRule['table'], $validationRule['column']);
                                } elseif ($key === 'unique') {
                                    $rules[$headerName][] = Rule::unique($validationRule['table'], $validationRule['column']);
                                } elseif ($key === 'in') {
                                    $rules[$headerName][] = Rule::in($validationRule);
                                }
                            }
                        }
                    }
                    else {
                        $typeValidator[$headerName] = $typeMessage;
                    }
                }

                if (!empty($typeValidator)) {
                    foreach ($typeValidator as $column => $message) {
                        $errorItems[] = [
                            'import_id'   => $importId,
                            'value'       => $data[$column] ?? '',
                            'message'     => $message,
                            'file_row'    => $iterator,
                            'file_column' => $column,
                            'created_at'  => Carbon::now()
                        ];
                    }
                    continue;
                }

                $validator = Validator::make($data, $rules);

                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $column => $messages) {
                        $errorItems[] = [
                            'import_id'   => $importId,
                            'value'       => $data[$column] ?? '',
                            'message'     => implode(', ', $messages),
                            'file_row'    => $iterator,
                            'file_column' => $column,
                            'created_at'  => Carbon::now()
                        ];
                    }
                    continue;
                }

                try {
                    if ($updateOrCreateFields) {
                        $keys = [];
                        foreach ($updateOrCreateFields as $field) {
                            $keys[$field] = $data[$field] ?? null;
                        }

                        $existing = $model->where($keys)->first();

                        if ($existing) {
                            $changes = [];
                            foreach ($data as $key => $value) {
                                if (isset($existing->$key) && $existing->$key != $value) {
                                    $changes[$key] = [
                                        'old_value' => $existing->$key,
                                        'new_value' => $value,
                                    ];
                                }
                            }

                            $model->where($keys)->update($data);

                            if (!empty($changes)) {
                                foreach ($changes as $columnName => $change) {
                                    $auditItems[] = [
                                        'import_id'   => $importId,
                                        'object_id'   => $existing->id,
                                        'import_type' => $importType,
                                        'file_row'    => $iterator,
                                        'file_column' => $columnName,
                                        'old_value'   => $change['old_value'],
                                        'new_value'   => $change['new_value'],
                                        'created_at'  => Carbon::now()
                                    ];
                                }
                            }
                        } else {
                            $data['created_at']  = Carbon::now();
                            $model->insert($data);
                        }
                    } else {
                        $data['created_at']  = Carbon::now();
                        $model->insert($data);
                    }
                } catch (\Throwable $ex) {
                    $errorItems[] = [
                        'import_id'   => $importId,
                        'value'       => json_encode($data),
                        'message'     => $ex->getMessage(),
                        'file_row'    => $iterator,
                        'file_column' => null,
                        'created_at'  => Carbon::now()
                    ];
                    continue;
                }
            }
        }

        if (!empty($auditItems)) {
            AuditLog::insert($auditItems);
        }

        if (!empty($errorItems)) {
            ErrorLog::insert($errorItems);
        }
    }

    /**
     * Get error messages by data type
     *
     * @param array $fieldConfig
     * @param mixed $value
     * @return string|null
     */
    public function getDataTypeErrorMessage(array $fieldConfig, mixed $value): ?string
    {
        if (isset($fieldConfig['type'])) {
            $expectedType = $fieldConfig['type'];

            switch ($expectedType) {
                case 'int':
                case 'integer':
                    if (!is_numeric($value) || intval($value) != $value) {
                        return 'Wrong data type, expected integer';
                    }
                    break;

                case 'float':
                case 'double':
                    if (!is_numeric($value)) {
                        return 'Wrong data type, expected float/double';
                    }
                    break;

                case 'bool':
                    $boolVal = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if (!is_bool($boolVal)) {
                        return 'Wrong data type, expected boolean';
                    }
                    break;

                case 'date':
                    try {
                        $parsed = Carbon::parse($value);
                        if (!($parsed->toDateString() === $value || $parsed->format('Y-m-d') === $value)) {
                            return 'Wrong data type, expected date (Y-m-d)';
                        }
                    } catch (\Exception $ex) {
                        return 'Wrong data type, expected date (Y-m-d)';
                    }
                    break;

                case 'datetime':
                    try {
                        $parsed = Carbon::parse($value);
                        if (!($parsed->toDateTimeString() === $value || $parsed->format('Y-m-d H:i:s') === $value)) {
                            return 'Wrong data type, expected datetime (Y-m-d H:i:s)';
                        }
                    } catch (\Exception $ex) {
                        return 'Wrong data type, expected datetime (Y-m-d H:i:s)';
                    }
                    break;

                default:
                    break;
            }
        }
        return null;
    }

    /**
     * Get imported data items
     *
     * @param $importType
     * @param $fileKey
     * @param $headers
     * @param $term
     * @return LengthAwarePaginator
     */
    public function getImportedDataItems($importType, $fileKey, $headers, $term): LengthAwarePaginator
    {
        $modelClass = $this->getDataTableModel($importType, $fileKey);

        $query = $modelClass::query();

        $term = trim($term);
        if (strlen($term) > 2) {
            $query->where(function ($subQuery) use ($headers, $term) {
                foreach (array_keys($headers) as $header) {
                    $subQuery->orWhere($header, 'like', "%$term%");
                }
            });
        }

        return $query->paginate(30)->withQueryString();
    }

    /**
     * Get datatable model
     *
     * @param string $importType
     * @param string $fileKey
     * @return mixed
     */
    public function getDataTableModel(string $importType, string $fileKey): mixed
    {
        // According to the task, everything should work dynamically except:
        // - Model creation and corresponding database tables (for each new import type)
        // - Permission creation (for new import types)
        //
        // Therefore, I extended the configuration to hold the model object,
        // even if it is not directly used elsewhere.
        //
        // If extending the existing import configuration is not allowed,
        // we can define the models here as shown in "method 2" below.

        $config = $this->getFileImportConfiguration($importType, $fileKey);

        return $config['model'];

        /* Method 2:
        return match ($importType) {
            'orders' => new Order(),
            'products' => new Product(),
            'prices' => match ($fileKey) {
                'file1' => new Price(),
                'file2' => new PriceDiscount(),
                default => null,
            },
            default => null,
        };
        */
    }

    /**
     * Get all import configuration
     *
     * @param string $importType
     * @return array
     */
    private function getImportConfig(string $importType): array
    {
        return config("imports.$importType");
    }

    /**
     * Get file import configuration
     *
     * @param string $importType
     * @param string $fileKey
     * @return array
     */
    private function getFileImportConfiguration(string $importType, string $fileKey): array
    {
        return config("imports.$importType.files.$fileKey");
    }

    /**
     * Get file import configuration
     *
     * @param string $importType
     * @param string $fileKey
     * @return array
     */
    public function getImportHeaderConfig(string $importType, string $fileKey): array
    {
        $config = $this->getFileImportConfiguration($importType, $fileKey);

        return $config['headers_to_db'];
    }
}
