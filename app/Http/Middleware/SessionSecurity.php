<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionSecurity
{
    private const ABSOLUTE_TIMEOUT_SECONDS = 14400;

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $createdAt = (int) $request->session()->get('session_created_at', time());
        $request->session()->put('session_created_at', $createdAt);

        if (time() - $createdAt > self::ABSOLUTE_TIMEOUT_SECONDS) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('status', 'Tu sesión expiró por seguridad.');
        }

        return $next($request);
    }
}
