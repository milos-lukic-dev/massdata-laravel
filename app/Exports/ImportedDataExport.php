<?php

namespace App\Exports;

use App\Services\ImportService;
use Illuminate\Support\Collection;
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
        $this->importService = new ImportService();
        $this->importType = $importType;
        $this->fileKey = $fileKey;
        $this->term = $term;
    }

    /**
     * Return the data collection to be exported.
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        $model = $this->importService->getDataTableModel($this->importType, $this->fileKey);
        $query = $model->newQuery();

        $term = trim($this->term);
        if (strlen($term) > 2) {
            $query->where(function ($subQuery) use ($model, $term) {
                foreach ($model->getFillable() as $column) {
                    $subQuery->orWhere($column, 'like', '%' . $term . '%');
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
        $headers = $this->importService->getImportHeaderConfig($this->importType, $this->fileKey);

        return array_keys($headers ?? []);
    }
}
