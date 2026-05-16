<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RememberPrivateRoute
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && ! $request->expectsJson()) {
            $request->session()->put('last_private_url', $request->fullUrl());
        }

        return $next($request);
    }
}
