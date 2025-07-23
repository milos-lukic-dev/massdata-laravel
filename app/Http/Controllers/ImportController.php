<?php

namespace App\Http\Controllers;

use App\Exports\ImportedDataExport;
use App\Http\Requests\Import\UploadFilesRequest;
use App\Models\AuditLog;
use App\Models\ErrorLog;
use App\Models\ImportLog;
use App\Services\ImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Controller responsible for handling data imports, audits, and logs.
 *
 * @class ImportController
 */
class ImportController extends Controller
{
    /**
     * Import
     *
     * @param ImportService $importService
     * @return View
     */
    public function import(ImportService $importService): View
    {
        $availableImportTypes = $importService->getImportTypes(false);

        return view('imports.import', compact('availableImportTypes'));
    }

    /**
     * Upload
     *
     * @param UploadFilesRequest $request
     * @param ImportService $importService
     * @return RedirectResponse
     */
    public function upload(UploadFilesRequest $request, ImportService $importService): RedirectResponse
    {
        $message = $importService->upload([
            'import_type' => $request->input('import_type'),
            'files'       => $request->file('files'),
        ]);

        return redirect()->back()->with('notification', $message);
    }

    /**
     * Display imported data for specific type and file.
     *
     * @param Request $request
     * @param ImportService $importService
     * @param $importType
     * @param $fileKey
     * @return Container|mixed|object
     */
    public function importedData(Request $request, ImportService $importService, $importType, $fileKey)
    {
        $headers = $importService->getImportHeaderConfig($importType, $fileKey);

        $items = $importService->getImportedDataItems($importType, $fileKey, $headers, $request->get('q'));

        return view('imports.imported-data', compact('items', 'headers', 'importType', 'fileKey'));
    }

    /**
     * Return audit logs for a given import type and object ID.
     *
     * @param $importType
     * @param $objectId
     * @return mixed
     */
    public function getAudits($importType, $objectId)
    {
        return response()->json((new AuditLog())->getLogsByImportId($importType, $objectId));
    }

    /**
     * Delete a single imported record and its audit logs.
     */
    public function destroy(ImportService $importService, string $importType, string $fileKey, int $modelId): RedirectResponse
    {
        $modelClass = $importService->getDataTableModel($importType, $fileKey);
        $model = $modelClass->findOrFail($modelId);

        // We can expend this to checking relations
        // Example: We delete products, then need be deleted orders and audits for orders
        (new AuditLog())->deleteAuditsByModelAndId($importType, $modelId);

        $isDeleted = $model->delete();

        return redirect()->back()->with('notification', $isDeleted ? 'Row deleted successfully.' : 'Failed to delete row.');
    }

    /**
     * Export imported data to Excel.
     *
     * @param Request $request
     * @param string $importType
     * @param string $fileKey
     * @return BinaryFileResponse
     */
    public function export(Request $request, string $importType, string $fileKey): BinaryFileResponse
    {
        return Excel::download(
            new ImportedDataExport($importType, $fileKey, $request->get('q')),
            "{$importType}_{$fileKey}.xlsx"
        );
    }

    /**
     * Show all import logs.
     */
    public function importLogs(): View
    {
        $items = (new ImportLog())->getImports();

        return view('imports.log', compact('items'));
    }

    /**
     * Show detailed errors for a specific import log.
     */
    public function importLogErrors(int $logId): View
    {
        $import = (new ImportLog())->getImport($logId);
        $importErrors = (new ErrorLog())->getErrorsByImportId($import->id);

        return view('imports.log-error', compact('import', 'importErrors'));
    }
}
