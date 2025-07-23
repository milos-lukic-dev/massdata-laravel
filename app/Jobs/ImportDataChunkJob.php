<?php

namespace App\Jobs;

use App\Services\ImportService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job to process a chunk of import data rows asynchronously.
 */
class ImportDataChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * The type of import
     *
     * @var string
     */
    protected string $importType;

    /**
     * The rows to be processed in this chunk.
     *
     * @var array
     */
    protected array $rows;

    /**
     * The file key of the import file configuration.
     *
     * @var string
     */
    protected string $fileKey;

    /**
     * The import ID.
     *
     * @var int
     */
    protected int $importId;

    /**
     * The current chunk iteration index.
     *
     * @var int
     */
    protected int $iterator;

    /**
     * Create a new job instance.
     *
     * @param string $importType
     * @param array $rows
     * @param string $fileKey
     * @param int $importId
     * @param int $iterator
     */
    public function __construct(string $importType, array $rows, string $fileKey, int $importId, int $iterator)
    {
        $this->importType = $importType;
        $this->rows = $rows;
        $this->fileKey = $fileKey;
        $this->importId = $importId;
        $this->iterator = $iterator;
    }

    /**
     * Execute the job.
     *
     * @param ImportService $importService
     * @return void
     */
    public function handle(ImportService $importService): void
    {
        $importService->processRows(
            $this->importType,
            $this->rows,
            $this->fileKey,
            $this->importId,
            $this->iterator
        );
    }
}
