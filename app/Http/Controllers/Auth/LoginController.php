<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginController
{
    public function create(): View
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);

        Session::put('login_math_answer', $a + $b);

        return view('auth.login', [
            'mathQuestion' => "{$a} + {$b}",
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'math_answer' => ['required', 'integer'],
        ]);

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            Log::warning('Intento fallido de login', [
                'email' => $credentials['email'],
                'ip' => $request->ip(),
            ]);

            $this->failLogin();
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

        $expected = (int) $request->session()->pull('login_math_answer');

        if ((int) $request->input('math_answer') !== $expected) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::warning('Verificación anti-robot fallida', [
                'email' => $credentials['email'],
                'ip' => $request->ip(),
            ]);

            return redirect('/login')
                ->withInput($request->only('email'))
                ->withErrors(['math_answer' => 'Las credenciales no son válidas o la verificación no fue correcta.'])
                ->with('math_failed', true);
        }

        $request->session()->put('session_created_at', time());

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

    private function failLogin(): never
    {
        throw ValidationException::withMessages([
            'email' => 'Las credenciales no son válidas o la verificación no fue correcta.',
        ]);
    }
}
