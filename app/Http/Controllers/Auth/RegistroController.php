<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;
use Throwable;

class RegistroController
{
    public function notice(Request $request): View
    {
        $email = (string) ($request->query('email') ?: $request->user()?->email ?: '');

        return view('auth.verify-email', [
            'email' => $email,
            'maskedEmail' => $this->maskEmail($email),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:160', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => mb_strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);

        if (Schema::hasTable('user_profiles')) {
            $user->profile()->create([
                'primary_email' => $user->email,
                'country' => 'México',
            ]);
        }

        if (! $this->sendConfirmationEmail($user)) {
            $user->delete();

            return back()
                ->withInput($request->only('name', 'email'))
                ->withErrors(['email' => 'No pudimos enviar el correo de confirmación. Revisa la configuración SMTP en el servidor.']);
        }

        return redirect('/registro/confirmacion?email='.urlencode($user->email))
            ->with('status', 'Te enviamos un correo para confirmar tu cuenta antes de continuar.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc,dns', 'max:160'],
        ]);

        $user = User::query()->where('email', mb_strtolower($validated['email']))->first();

        if (! $user) {
            return back()->with('status', 'Si existe una cuenta pendiente, enviaremos un nuevo correo de confirmación.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('status', 'Tu correo ya fue confirmado. Puedes iniciar sesión.');
        }

        if (! $this->sendConfirmationEmail($user)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'No pudimos reenviar el correo. Revisa la configuración SMTP en el servidor.']);
        }

        return back()->with('status', 'Correo de confirmación reenviado. Revisa bandeja de entrada y spam.');
    }

    public function confirm(Request $request, int $id, string $hash): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        $user = User::query()->findOrFail($id);

        abort_unless(hash_equals($hash, sha1($user->getEmailForVerification())), 403);

        abort_if($user->hasVerifiedEmail(), 403);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        Log::info('Correo confirmado', ['user_id' => $user->getKey()]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/registro/confirmado');
    }

    private function sendConfirmationEmail(User $user): bool
    {
        $confirmationUrl = URL::temporarySignedRoute(
            name: 'verification.confirm',
            expiration: now()->addHours(24),
            parameters: [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );

        try {
            Mail::send('emails.confirm-account', [
                'name' => $user->name,
                'confirmationUrl' => $confirmationUrl,
            ], function ($message) use ($user): void {
                $message->to($user->email)->subject('Confirma tu cuenta de ContaPro');
            });
        } catch (Throwable $exception) {
            Log::error('No se pudo enviar correo de confirmación', [
                'user_id' => $user->getKey(),
                'exception' => $exception,
            ]);

            return false;
        }

        return true;
    }

    private function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return 'c***@dominio.com';
        }

        [$local, $domain] = explode('@', $email, 2);
        $first = mb_substr($local, 0, 1) ?: 'c';

        return $first.'***@'.$domain;
    }
}
