<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectAuthenticatedToDashboard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = $request->user()?->loadMissing('profile');

        if (! $user?->hasVerifiedEmail()) {
            return redirect('/registro/confirmacion?email='.urlencode((string) $user?->email));
        }

        if (! $user->hasCompleteProfile()) {
            return redirect('/perfil');
        }

        return redirect('/dashboard');
    }
}
