<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the Vitahr admin panel: the authenticated vitahr user must hold
 * the 'manage employees' permission on the shared vitahr database.
 */
class EnsureUserIsVitahrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();

        if ($user === null || ! $user->hasVitahrPermission('manage employees')) {
            abort(403);
        }

        return $next($request);
    }
}
