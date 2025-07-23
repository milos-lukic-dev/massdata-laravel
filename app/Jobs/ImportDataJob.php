<?php

namespace App\Jobs;

use App\Services\ImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Job responsible for processing import data files asynchronously.
 */
class ImportDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The type of import (e.g., users, products).
     *
     * @var string
     */
    protected string $importType;

    /**
     * The relative file path to the import file in storage.
     *
     * @var string
     */
    protected string $filePath;

    /**
     * The key identifying the import file configuration.
     *
     * @var string
     */
    protected string $fileKey;

    /**
     * The ID of the import record for tracking.
     *
     * @var int
     */
    protected int $importId;

    /**
     * Create a new job instance.
     *
     * @param string $importType
     * @param string $filePath
     * @param string $fileKey
     * @param int $importId
     */
    public function __construct(string $importType, string $filePath, string $fileKey, int $importId)
    {
        $this->importType = $importType;
        $this->filePath = $filePath;
        $this->fileKey = $fileKey;
        $this->importId = $importId;
    }

    /**
     * Execute the job.
     *
     * @param ImportService $importService
     * @return void
     */
    public function handle(ImportService $importService): void
    {
        $absolutePath = storage_path('app/private/' . $this->filePath);

        // Or we can send email if file doesn't exist.
        if (!Storage::exists($this->filePath)) {
            Log::error("Import file does not exist: {$this->filePath}");
            return;
        }

        $importService->processFile($absolutePath, $this->importType, $this->fileKey, $this->importId);

        Storage::delete($this->filePath);
    }
}
