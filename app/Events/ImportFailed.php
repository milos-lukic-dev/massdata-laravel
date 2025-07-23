<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event triggered when an import process fails.
 *
 * This event carries the failed import instance and the related error rows
 * to allow listeners to handle failure-related logic such as logging or notifications.
 *
 * @class ImportFailed
 */
class ImportFailed
{
    use Dispatchable, SerializesModels;

    /**
     * The import instance.
     *
     * @var mixed
     */
    public $import;

    /**
     * The rows that failed during the import.
     *
     * @var array
     */
    public array $errorRows;

    /**
     * Create a new event instance.
     *
     * @param mixed $import
     * @param array $errorRows
     */
    public function __construct($import, array $errorRows)
    {
        $this->import    = $import;
        $this->errorRows = $errorRows;
    }
}
