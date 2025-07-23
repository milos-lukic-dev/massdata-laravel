<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Import Log model
 *
 * @class ImportLog
 */
class ImportLog extends Model
{
    protected $fillable = [
        'user_id',
        'import_type',
        'file_key',
        'file_name',
        'status',
    ];

    /**
     * Relation to the user who performed the import.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Retrieve all import logs with pagination.
     *
     * @return LengthAwarePaginator
     */
    public function getImports(): LengthAwarePaginator
    {
        return $this->with('user')->orderByDesc('id')->paginate(50);
    }

    /**
     * Retrieve a specific import log by ID.
     *
     * @param int $importId
     * @return ImportLog
     */
    public function getImport(int $importId): ImportLog
    {
        return $this->findOrFail($importId);
    }
}
