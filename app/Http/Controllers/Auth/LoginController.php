<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController
{
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            Log::warning('Intento fallido de login', [
                'email' => $credentials['email'],
                'ip' => $request->ip(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'Las credenciales no son válidas.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/registro/confirmacion?email='.urlencode($user->email))
                ->with('status', 'Confirma tu correo electrónico antes de continuar.');
        }

        Log::info('Login exitoso', [
            'user_id' => $user->getKey(),
            'ip' => $request->ip(),
        ]);

        return redirect()->intended('/dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
