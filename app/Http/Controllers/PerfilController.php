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
    public function edit(Request $request): View
    {
        if (! Schema::hasTable('user_profiles')) {
            return view('perfil.index', [
                'profile' => new UserProfile([
                    'primary_email' => $request->user()->email,
                    'country' => 'México',
                ]),
                'profileStorageUnavailable' => true,
            ]);
        }

        $profile = $request->user()->profile()->firstOrCreate([
            'user_id' => $request->user()->id,
        ], [
            'primary_email' => $request->user()->email,
            'country' => 'México',
        ]);

        return view('perfil.index', [
            'profile' => $profile,
            'profileStorageUnavailable' => false,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:160'],
            'rfc' => ['nullable', 'string', 'min:12', 'max:13', new RfcValido()],
            'user_type' => ['required', Rule::in(['Contador independiente', 'Despacho contable'])],
            'primary_email' => ['required', 'email:rfc,dns', 'max:160'],
            'country' => ['required', Rule::in(['México'])],
        ]);

        if (! Schema::hasTable('user_profiles')) {
            return redirect('/dashboard')->with('status', 'Perfil recibido. Falta activar almacenamiento de perfil en base de datos.');
        }

        $request->user()->profile()->updateOrCreate([
            'user_id' => $request->user()->id,
        ], [
            ...$validated,
            'completed_at' => now(),
        ]);

        return redirect('/dashboard');
    }
}
