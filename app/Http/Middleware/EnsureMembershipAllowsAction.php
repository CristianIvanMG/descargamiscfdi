<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureMembershipAllowsAction
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Schema::hasTable('user_profiles')) {
            return $next($request);
        }

        $profile = $request->user()?->loadMissing('profile')->profile;

        if ($profile?->user_type === 'Persona física') {
            return $next($request);
        }

        if ($this->hasActivePaidMembership((int) $request->user()->id)) {
            return $next($request);
        }

        return redirect('/suscripcion/upgrade')
            ->with('status', 'Tu perfil requiere un plan para usar descargas avanzadas, múltiples clientes y reportes.');
    }

    private function hasActivePaidMembership(int $userId): bool
    {
        $table = Schema::hasTable('suscripciones')
            ? 'suscripciones'
            : (Schema::hasTable('suscripcions') ? 'suscripcions' : null);

        if ($table === null) {
            return false;
        }

        try {
            return DB::table($table)
                ->where('user_id', $userId)
                ->whereIn('estatus', ['activa', 'activo', 'active', 'paid'])
                ->whereIn('plan', ['pro', 'despacho'])
                ->where(function ($query): void {
                    $query->whereNull('periodo_fin')
                        ->orWhere('periodo_fin', '>', now());
                })
                ->exists();
        } catch (Throwable) {
            return false;
        }
    }
}
