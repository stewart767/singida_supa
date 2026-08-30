<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return $next($request);
        }

        $route = $request->route();
        $action = $route?->getActionMethod();
        $controller = $route?->getController();

        if ($controller instanceof \App\Http\Controllers\Web\AdminWebController) {
            switch ($action) {
                case 'dashboard':
                    abort_unless($user->hasPermissionTo('view_dashboard'), 403);
                    break;

                case 'applications':
                case 'showApplication':
                case 'storeApplication':
                    abort_unless($user->hasPermissionTo('manage_applications')
                        || $user->hasPermissionTo('verify_documents')
                        || $user->hasPermissionTo('make_admission_decisions'), 403);
                    break;

                case 'payments':
                    abort_unless($user->hasPermissionTo('verify_payments'), 403);
                    break;

                case 'programmes':
                case 'storeProgramme':
                case 'updateProgramme':
                case 'deleteProgramme':
                    abort_unless($user->hasPermissionTo('manage_programmes'), 403);
                    break;

                case 'exportPdfReport':
                    abort_unless($user->hasPermissionTo('download_reports'), 403);
                    break;

                case 'cms':
                    abort_unless($user->hasPermissionTo('manage_settings') || $user->hasPermissionTo('view_reports'), 403);
                    break;

                default:
                    abort_unless($user->hasPermissionTo('manage_settings'), 403);
                    break;
            }
        }

        return $next($request);
    }
}
