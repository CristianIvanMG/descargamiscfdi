@extends('layouts.app', [
    'title' => 'Iniciar sesión | ContaPro',
    'metaDescription' => 'Acceso seguro a ContaPro para descargar y organizar CFDI emitidos y recibidos del SAT.',
    'hideNav' => true,
])

@section('content')
    <section class="auth-screen">
        <div class="auth-shell">
            <a class="back-home-button" href="{{ url('/') }}" aria-label="Volver a Home">←</a>
            <a class="auth-brand" href="{{ url('/') }}">ContaPro</a>
            <div class="auth-panel-card">
                <div class="auth-copy">
                    <span class="eyebrow">Acceso fiscal</span>
                    <h1>Acceso a tu plataforma de CFDI</h1>
                    <p>Descarga y organiza CFDI emitidos y recibidos del SAT.</p>
                    <div class="auth-trust">Tus datos fiscales se manejan de forma segura.</div>
                </div>

                <form class="auth-card" action="{{ url('/login') }}" method="post" aria-label="Iniciar sesión en ContaPro">
                    @csrf
                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>

                    <label for="password">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>

                    <label for="math_answer">Verificación anti-robot: {{ $mathQuestion }} = ?</label>
                    <div class="math-field-wrap {{ session('math_failed') ? 'math-error' : '' }}">
                        <input id="math_answer" name="math_answer" type="number" inputmode="numeric" required aria-label="Resultado de la suma">
                        <span aria-hidden="true">!</span>
                    </div>

                    <button class="btn btn-primary btn-full" type="submit">Iniciar sesión</button>

                    @if ($errors->any())
                        <div class="auth-error">Las credenciales no son válidas o la verificación no fue correcta.</div>
                    @endif

                    @if (session('status'))
                        <div class="auth-success">{{ session('status') }}</div>
                    @endif

                    <div class="auth-links">
                        <a href="{{ url('/registro') }}">Crear cuenta</a>
                        <a href="{{ url('/recuperar') }}">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
