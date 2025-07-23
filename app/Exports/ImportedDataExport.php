<?php

namespace App\Exports;

use App\Services\ImportService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Export class for exporting imported data to Excel.
 *
 * This export supports optional filtering by search term and provides column headings
 * based on the import configuration.
 *
 * @class ImportedDataExport
 */
class ImportedDataExport implements FromCollection, WithHeadings
{
    protected string $importType;
    protected string $fileKey;
    protected ?string $term;

    protected ImportService $importService;

    /**
     * Create a new export instance.
     *
     * @param string $importType
     * @param string $fileKey
     * @param string|null $term
     */
    public function __construct(string $importType, string $fileKey, ?string $term = null)
    {
        $this->importType = $importType;
        $this->fileKey = $fileKey;
        $this->term = $term;

        $this->importService = App::make(ImportService::class);
    }

    /**
     * Return the data collection to be exported.
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        $model = $this->importService->getDataTableModel($this->importType, $this->fileKey);

        if (!$model) {
            return collect();
        }

        $query = $model->newQuery();

        if (!empty($this->term)) {
            $query->where(function ($subQuery) use ($model) {
                foreach ($model->getFillable() as $column) {
                    $subQuery->orWhere($column, 'like', '%' . $this->term . '%');
                }
            });
        }

        $columns = collect($model->getFillable())
            ->reject(fn($col) => in_array($col, ['id', 'created_at', 'updated_at']))
            ->values()
            ->all();

        return $query->get($columns);
    }

    /**
     * Return the column headings for the Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        $config = config("imports.{$this->importType}.files.{$this->fileKey}");

        if (!$config) {
            abort(404, 'Import configuration not found.');
        }

        return array_keys($config['headers_to_db'] ?? []);
    }
}
