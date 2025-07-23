<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * Audit Log model
 *
 * @class AuditLog
 */
class AuditLog extends Model
{
    protected $fillable = [
        'import_id',
        'object_id',
        'import_type',
        'file_row',
        'file_column',
        'old_value',
        'new_value',
    ];

    /**
     * Get the import associated with this audit log.
     */
    public function import(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class, 'import_id');
    }

    /**
     * Get audit logs by import type and object ID.
     *
     * @param string $importType
     * @param int $objectId
     * @return Collection
     */
    public function getLogsByImportId(string $importType, int $objectId): Collection
    {
        return $this->with(['import.user'])
            ->where('object_id', $objectId)
            ->where('import_type', $importType)
            ->orderByDesc('id')
            ->get()
            ->map(function ($log) {
                return [
                    'old_value'   => $log->old_value,
                    'new_value'   => $log->new_value,
                    'created_at'  => $log->created_at->toDateTimeString(),
                    'file_row'    => $log->file_row,
                    'file_column' => $log->file_column,
                    'user_name'   => optional($log->import?->user)->name ?? '-',
                    'file_name'   => $log->import?->file_name ?? '-',
                ];
            });
    }

    /**
     * Delete audit logs by import type and model ID.
     *
     * @param string $importType
     * @param int $modelId
     * @return void
     */
    public function deleteAuditsByModelAndId(string $importType, int $modelId): void
    {
        $this->where('import_type', $importType)
            ->where('object_id', $modelId)
            ->delete();
    }
}
