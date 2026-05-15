@extends('layouts.app', [
    'title' => 'Confirma tu correo | ContaPro',
    'hideNav' => true,
])

@section('content')
    <section class="auth-screen">
        <div class="auth-shell narrow-auth">
            <a class="auth-brand" href="{{ url('/') }}">ContaPro</a>
            <div class="auth-message-card">
                <span class="eyebrow">Confirmación pendiente</span>
                <h1>Confirma tu correo electrónico</h1>
                <p>Para proteger tu información fiscal, necesitamos confirmar tu correo antes de continuar.</p>
                <div class="auth-mail-target">Enviado a: <strong>{{ $maskedEmail }}</strong></div>

                @if (session('status'))
                    <div class="auth-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-error">{{ $errors->first() }}</div>
                @endif

                <form action="{{ url('/registro/reenviar-confirmacion') }}" method="post" class="resend-form">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button class="btn btn-primary" type="submit">Reenviar correo de confirmación</button>
                </form>

                <p class="auth-help">¿No lo recibiste? Revisa tu carpeta de spam.</p>
                <div class="auth-trust-light">Este paso es obligatorio para mantener la privacidad de tu información.</div>
                <a class="auth-secondary-link" href="{{ url('/login') }}">Volver al login</a>
            </div>
        </div>
    </section>
@endsection
