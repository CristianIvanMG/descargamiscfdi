<?php

namespace App\Http\Middleware;

use App\Support\MembershipAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMembershipAllowsAction
{
    public function handle(Request $request, Closure $next): Response
    {
        if (MembershipAccess::canUseMultipleRfcs($request->user())) {
            return $next($request);
        }

        return redirect('/suscripcion/upgrade')->with(
            'status',
            'El módulo de clientes y múltiples RFC requiere un plan activo. Puedes seguir descargando CFDI del RFC de tu perfil.'
        );
    }
}
