<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Rules\RfcValido;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PerfilController
{
    public const ESTADOS_MEXICO = [
        'Aguascalientes',
        'Baja California',
        'Baja California Sur',
        'Campeche',
        'Chiapas',
        'Chihuahua',
        'Ciudad de Mexico',
        'Coahuila',
        'Colima',
        'Durango',
        'Estado de Mexico',
        'Guanajuato',
        'Guerrero',
        'Hidalgo',
        'Jalisco',
        'Michoacan',
        'Morelos',
        'Nayarit',
        'Nuevo Leon',
        'Oaxaca',
        'Puebla',
        'Queretaro',
        'Quintana Roo',
        'San Luis Potosi',
        'Sinaloa',
        'Sonora',
        'Tabasco',
        'Tamaulipas',
        'Tlaxcala',
        'Veracruz',
        'Yucatan',
        'Zacatecas',
    ];

    public function edit(Request $request): View
    {
        if (! Schema::hasTable('user_profiles')) {
            return view('perfil.index', [
                'profile' => new UserProfile([
                    'primary_email' => $request->user()->email,
                ]),
                'estados' => self::ESTADOS_MEXICO,
                'profileStorageUnavailable' => true,
            ]);
        }

        $profile = $request->user()->profile()->firstOrCreate([
            'user_id' => $request->user()->id,
        ], [
            'primary_email' => $request->user()->email,
        ]);

        return view('perfil.index', [
            'profile' => $profile,
            'estados' => self::ESTADOS_MEXICO,
            'profileStorageUnavailable' => false,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->user()->loadMissing('profile');
        $wasComplete = $request->user()->profile?->isComplete() ?? false;

        $validated = $request->validate([
            'business_name' => [$wasComplete ? 'nullable' : 'required', 'string', 'max:160', 'regex:/^[\pL\s]+$/u'],
            'rfc' => [$wasComplete ? 'nullable' : 'required', 'string', 'min:12', 'max:13', new RfcValido()],
            'user_type' => [$wasComplete ? 'nullable' : 'required', Rule::in(['Contador independiente', 'Persona fisica', 'Despacho contable'])],
            'primary_email' => ['required', 'email:rfc,dns', 'max:160'],
            'country' => ['required', Rule::in(self::ESTADOS_MEXICO)],
        ]);

        if ($wasComplete && $request->user()->profile) {
            $validated['business_name'] = $request->user()->profile->business_name;
            $validated['rfc'] = $request->user()->profile->rfc;
            $validated['user_type'] = $request->user()->profile->user_type;
        } else {
            $validated['business_name'] = mb_strtoupper((string) $validated['business_name'], 'UTF-8');
            $validated['rfc'] = mb_strtoupper((string) $validated['rfc'], 'UTF-8');
        }

        if (! Schema::hasTable('user_profiles')) {
            return redirect('/dashboard')->with('status', 'Perfil recibido. Falta activar almacenamiento de perfil en base de datos.');
        }

        $request->user()->profile()->updateOrCreate([
            'user_id' => $request->user()->id,
        ], [
            ...$validated,
            'completed_at' => now(),
        ]);

        return $wasComplete
            ? back()->with('status', 'Perfil actualizado.')
            : redirect('/dashboard');
    }
}
