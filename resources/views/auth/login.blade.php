@extends('layouts.app', [
    'title' => 'Iniciar sesión | ContaPro',
    'metaDescription' => 'Acceso seguro a ContaPro para descargar y organizar CFDI emitidos y recibidos del SAT.',
    'hideNav' => true,
])

@section('content')
    <section class="auth-screen">
        <div class="auth-shell">
            <a class="auth-brand" href="{{ url('/') }}">ContaPro</a>
            <div class="auth-panel-card">
                <div class="auth-copy">
                    <span class="eyebrow">Acceso fiscal</span>
                    <h1>Acceso a tu plataforma de CFDI</h1>
                    <p>Descarga y organiza CFDI emitidos y recibidos del SAT.</p>
                    <div class="auth-trust">Tus datos fiscales se manejan de forma segura.</div>
                </div>

                <form class="auth-card" action="{{ url('/dashboard') }}" method="get" aria-label="Iniciar sesión en ContaPro">
                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" autocomplete="email" required>

                    <label for="password">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>

                    <button class="btn btn-primary btn-full" type="submit">Iniciar sesión</button>
                    <div class="auth-links">
                        <a href="{{ url('/registro') }}">Crear cuenta</a>
                        <a href="{{ url('/recuperar') }}">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
