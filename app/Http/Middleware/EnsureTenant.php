<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return new RedirectResponse('/login');
        }

        $user = auth()->user();

        if ($request->routeIs('select-company')) {
            return $next($request);
        }

        if (!$user->tenant_id) {
            if ($request->routeIs('setup.*')) {
                return $next($request);
            }
            return new RedirectResponse('/setup');
        }

        $sessionTenantId = session('current_tenant_id');
        if (!$sessionTenantId || $sessionTenantId != $user->tenant_id) {
            session(['current_tenant_id' => $user->tenant_id]);
        }

        return $next($request);
    }
}
