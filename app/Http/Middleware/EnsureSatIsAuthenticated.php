<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSatIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->boolean('sat_authenticated')) {
            return $next($request);
        }

        return redirect('/dashboard')->with('status', 'Primero valida tu e.firma para conectar con el SAT.');
    }
}
