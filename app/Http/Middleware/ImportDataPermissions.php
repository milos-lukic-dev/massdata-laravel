<?php

namespace App\Http\Middleware;

use App\Services\ImportService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to restrict access if no import types are available for the current user.
 *
 * @class ImportDataPermissions
 */
class ImportDataPermissions
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
     * If no import types are available for the current user, deny access.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableImportTypes = $this->importService->getImportTypes(false);

        if (empty($availableImportTypes)) {
            abort(403, 'You do not have permission to access any import types.');
        }

        return $next($request);
    }
}
