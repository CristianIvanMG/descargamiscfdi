# PASO 8 — MIDDLEWARE · CONTROL DE PLANES
## ContadorMx · xml.contadormx.net

---

## MIDDLEWARE — CONTROL DE PLANES

```php
<?php
// app/Http/Middleware/RequierePlan.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequierePlan
{
    public function handle(Request $request, Closure $next, string ...$planes): mixed
    {
        $usuario = $request->user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        // Verificar que el plan no haya vencido
        if ($usuario->plan !== 'gratis' && $usuario->plan_expires_at?->isPast()) {
            $usuario->update(['plan' => 'gratis']);
        }

        if (!in_array($usuario->plan, $planes)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error'    => 'Plan insuficiente',
                    'plan_req' => $planes,
                    'upgrade'  => route('suscripcion.planes'),
                ], 403);
            }
            return redirect()->route('suscripcion.planes')
                ->with('aviso', 'Esta función requiere el plan ' . implode(' o ', $planes));
        }

        // Verificar límite de RFCs
        $config = config("planes.{$usuario->plan}");
        if (isset($config['max_rfcs']) && $config['max_rfcs'] < 999) {
            $totalRfcs = $usuario->rfcs()->count();
            if ($totalRfcs >= $config['max_rfcs'] && $request->routeIs('rfcs.store')) {
                return back()->with('error', 'Límite de RFC alcanzado para tu plan actual.');
            }
        }

        return $next($request);
    }
}
```
