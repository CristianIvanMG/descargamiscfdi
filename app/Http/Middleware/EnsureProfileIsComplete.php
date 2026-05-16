<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user()?->loadMissing('profile');

        if (! $user?->hasCompleteProfile()) {
            Log::info('Usuario redirigido a perfil incompleto', [
                'user_id' => $user?->getKey(),
            ]);

            return redirect('/perfil')->with('status', 'Completa tu perfil para empezar a trabajar con CFDI.');
        }

        return $next($request);
    }
}
