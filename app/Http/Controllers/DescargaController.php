<?php

namespace App\Http\Controllers;

use App\Models\DescargaJob;
use App\Rules\RfcValido;
use App\Support\MembershipAccess;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DescargaController
{
    public function create(Request $request): View
    {
        $request->user()->loadMissing('profile');

        return view('descarga.nueva', [
            'profile' => $request->user()->profile,
            'canUseMultipleRfcs' => MembershipAccess::canUseMultipleRfcs($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rfc' => ['required', 'string', 'min:12', 'max:13', new RfcValido()],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'tipo' => ['required', 'in:emitidos,recibidos'],
            'signed_token' => ['required', 'string'],
        ]);

        $user = $request->user()->loadMissing('profile');
        $requestedRfc = Str::upper($validated['rfc']);
        $canUseMultipleRfcs = MembershipAccess::canUseMultipleRfcs($user);

        if (! $canUseMultipleRfcs && ! MembershipAccess::isOwnProfileRfc($user, $requestedRfc)) {
            return back()
                ->withInput()
                ->withErrors(['rfc' => 'En modo gratuito solo puedes descargar CFDI del RFC registrado en tu perfil.']);
        }

        if (! $canUseMultipleRfcs && ! $this->isFreeRangeAllowed($validated['fecha_inicio'], $validated['fecha_fin'])) {
            return back()
                ->withInput()
                ->withErrors(['fecha_inicio' => 'El modo gratuito permite descargar máximo 6 meses dentro del año en curso.']);
        }

        return back()->with('status', __('app.descarga.pending_schema'));
    }

    private function isFreeRangeAllowed(string $startDate, string $endDate): bool
    {
        $start = CarbonImmutable::parse($startDate)->startOfDay();
        $end = CarbonImmutable::parse($endDate)->startOfDay();
        $currentYear = now()->year;

        if ((int) $start->year !== $currentYear || (int) $end->year !== $currentYear) {
            return false;
        }

        return $end->lessThanOrEqualTo($start->addMonthsNoOverflow(6));
    }

    public function show(DescargaJob $descargaJob): View
    {
        return view('descarga.estado', [
            'descarga' => $descargaJob,
        ]);
    }
}
