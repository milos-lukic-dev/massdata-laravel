<?php

namespace App\Http\Middleware;

use App\Services\ImportService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to authorize deletion of imported records based on dynamic permission.
 *
 * @class DeleteImportedRecord
 */
class DeleteImportedRecord
{
    /**
     * The import service instance used to retrieve import types.
     *
     * @var ImportService
     */
    protected ImportService $importService;

    /**
     * Create a new middleware instance.
     *
     * @param ImportService $importService
     */
    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $importType = $request->route('importType');

        $config = $this->importService->getImportConfig($importType);

        if (!$user || !$user->can($config['permission_required'])) {
            abort(403);
        }

        return $next($request);
    }
}
