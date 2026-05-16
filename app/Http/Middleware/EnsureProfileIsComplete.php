<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (! Schema::hasTable('user_profiles')) {
                return $next($request);
            }

            $user = $request->user()?->loadMissing('profile');

            if (! $user?->hasCompleteProfile()) {
                Log::info('Usuario redirigido a perfil incompleto', [
                    'user_id' => $user?->getKey(),
                ]);

                return redirect('/perfil')->with('status', 'Completa tu perfil para empezar a trabajar con CFDI.');
            }
        } catch (Throwable $exception) {
            Log::error('No se pudo validar perfil de usuario', [
                'user_id' => $request->user()?->getKey(),
                'exception' => $exception,
            ]);

            return $next($request);
        }

        return $next($request);
    }
}
