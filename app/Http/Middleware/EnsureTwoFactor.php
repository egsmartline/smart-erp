<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces mandatory two-factor authentication.
 *
 * - A user who has not completed setup is kept on the security page.
 * - A user with setup completed must have passed the challenge in this session.
 */
class EnsureTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if (!$user->hasTwoFactorEnabled()) {
            if ($request->routeIs('security.*') || $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route('security.index');
        }

        if ((int) $request->session()->get('2fa.verified') === (int) $user->id) {
            return $next($request);
        }

        // Authenticated session that never passed the challenge (e.g. session
        // created before 2FA was introduced). Force a fresh challenge.
        $remember = $request->session()->get('2fa.remember', false);

        Auth::logout();
        $request->session()->put('2fa.pending_id', $user->id);
        $request->session()->put('2fa.remember', (bool) $remember);
        $request->session()->regenerate();

        return redirect()->route('two-factor.login');
    }
}
