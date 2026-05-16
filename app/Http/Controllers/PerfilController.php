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
        'Ciudad de México',
        'Coahuila',
        'Colima',
        'Durango',
        'Estado de México',
        'Guanajuato',
        'Guerrero',
        'Hidalgo',
        'Jalisco',
        'Michoacán',
        'Morelos',
        'Nayarit',
        'Nuevo León',
        'Oaxaca',
        'Puebla',
        'Querétaro',
        'Quintana Roo',
        'San Luis Potosí',
        'Sinaloa',
        'Sonora',
        'Tabasco',
        'Tamaulipas',
        'Tlaxcala',
        'Veracruz',
        'Yucatán',
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
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:160', 'regex:/^[\pL\s]+$/u'],
            'rfc' => ['required', 'string', 'min:12', 'max:13', new RfcValido()],
            'user_type' => ['required', Rule::in(['Contador independiente', 'Persona física', 'Despacho contable'])],
            'primary_email' => ['required', 'email:rfc,dns', 'max:160'],
            'country' => ['required', Rule::in(self::ESTADOS_MEXICO)],
        ]);

        $validated['business_name'] = mb_strtoupper($validated['business_name'], 'UTF-8');

        if (! Schema::hasTable('user_profiles')) {
            return redirect('/dashboard')->with('status', 'Perfil recibido. Falta activar almacenamiento de perfil en base de datos.');
        }

        $wasComplete = $request->user()->profile?->isComplete() ?? false;

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
