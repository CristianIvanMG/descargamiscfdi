@extends('layouts.app', [
    'title' => 'Crear cuenta | ContaPro',
    'metaDescription' => 'Crea una cuenta en ContaPro para descargar, ordenar y analizar CFDI emitidos y recibidos del SAT.',
    'hideNav' => true,
])

@section('content')
    <section class="auth-screen">
        <div class="auth-shell">
            <a class="auth-brand" href="{{ url('/') }}">ContaPro</a>
            <div class="auth-panel-card">
                <div class="auth-copy">
                    <span class="eyebrow">Registro</span>
                    <h1>Crea tu cuenta de trabajo fiscal</h1>
                    <p>Primero confirma tu correo. Después podrás configurar tu perfil, RFC y clientes desde el panel privado.</p>
                    <div class="auth-trust">La cuenta se activa solo después de confirmar el correo.</div>
                </div>

                <form class="auth-card" action="{{ url('/registro') }}" method="post" aria-label="Registro ContaPro">
                    @csrf
                    <label for="name">Nombre completo</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required>

                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" value="{{ old('email', request('email')) }}" autocomplete="email" required>

                    <label for="password">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>

                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>

                    <button class="btn btn-primary btn-full" type="submit">Crear cuenta</button>
                    <p>Te enviaremos un correo para confirmar tu cuenta antes de continuar.</p>

                    @if ($errors->any())
                        <div class="auth-error">{{ $errors->first() }}</div>
                    @endif
                </form>
            </div>
        </div>
    </section>
@endsection
