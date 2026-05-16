<?php

namespace App\Http\Controllers;

use App\Models\DescargaJob;
use App\Models\Rfc;
use App\Rules\RfcValido;
use App\Support\MembershipAccess;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            'signed_token' => ['nullable', 'string'],
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
                ->withErrors(['fecha_inicio' => 'El modo gratuito permite descargar maximo 1 mes dentro del ano en curso.']);
        }

        $descarga = $this->storeDownloadRequest($user, $requestedRfc, $validated);

        return back()
            ->with('status', 'Solicitud registrada para seguimiento SAT. Guardamos el historial para evitar duplicar descargas.')
            ->with('show_donation_prompt', ! $this->hasActiveDonation((int) $user->id))
            ->with('descarga_id', $descarga?->getKey());
    }

    private function isFreeRangeAllowed(string $startDate, string $endDate): bool
    {
        $start = CarbonImmutable::parse($startDate)->startOfDay();
        $end = CarbonImmutable::parse($endDate)->startOfDay();
        $currentYear = now()->year;

        if ((int) $start->year !== $currentYear || (int) $end->year !== $currentYear) {
            return false;
        }

        return $end->lessThanOrEqualTo($start->addMonthNoOverflow());
    }

    private function storeDownloadRequest(mixed $user, string $rfc, array $validated): ?DescargaJob
    {
        if (! Schema::hasTable('rfcs') || ! Schema::hasTable('descarga_jobs')) {
            return null;
        }

        $client = Rfc::query()->firstOrCreate([
            'user_id' => $user->id,
            'rfc' => $rfc,
        ], [
            'razon_social' => $user->profile?->business_name ?: $rfc,
            'activo' => true,
        ]);

        return DescargaJob::query()->create([
            'user_id' => $user->id,
            'rfc_id' => $client->id,
            'estado' => 'pendiente',
            'tipo' => $validated['tipo'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'total_cfdi' => 0,
            'iniciado_en' => now(),
        ]);
    }

    private function hasActiveDonation(int $userId): bool
    {
        if (! Schema::hasTable('donations')) {
            return false;
        }

        return DB::table('donations')
            ->where('user_id', $userId)
            ->whereIn('estado', ['aprobada', 'approved', 'paid'])
            ->where('active_until', '>', now())
            ->exists();
    }

    public function show(DescargaJob $descargaJob): View
    {
        abort_unless((int) $descargaJob->user_id === (int) auth()->id(), 404);

        return view('descarga.estado', [
            'descarga' => $descargaJob,
        ]);
    }

    public function destroy(DescargaJob $descargaJob): RedirectResponse
    {
        abort_unless((int) $descargaJob->user_id === (int) auth()->id(), 404);

        $descargaJob->delete();

        return back()->with('status', 'Registro de descarga eliminado del historial.');
    }
}
