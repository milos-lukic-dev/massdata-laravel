<?php

namespace App\Http\Middleware;

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

        $config = config("imports.$importType");
        if (!$config) {
            abort(404);
        }

        $permission = $config['permission_required'];

        if (!$user || !$user->can($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
