<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Error Log model
 *
 * @class ErrorLog
 */
class ErrorLog extends Model
{
    protected $fillable = [
        'import_id',
        'value',
        'message',
        'file_row',
        'file_column',
        'created_at',
        'updated_at'
    ];

    /**
     * Retrieve paginated error logs for a specific import.
     *
     * @param int $importId
     * @return LengthAwarePaginator
     */
    public function getErrorsByImportId(int $importId): LengthAwarePaginator
    {
        return $this->where('import_id', $importId)
            ->orderByDesc('id')
            ->paginate(50);
    }
}
