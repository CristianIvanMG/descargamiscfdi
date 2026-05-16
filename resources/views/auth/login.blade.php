@extends('layouts.app', [
    'title' => 'Iniciar sesión | ContaPro',
    'metaDescription' => 'Acceso seguro a ContaPro para descargar y organizar CFDI emitidos y recibidos del SAT.',
    'hideNav' => true,
])

@section('content')
    @php
        if (! isset($mathQuestion)) {
            $mathA = random_int(2, 9);
            $mathB = random_int(2, 9);
            session()->put('login_math_answer', $mathA + $mathB);
            $mathQuestion = "{$mathA} + {$mathB}";
        }
    @endphp

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
                    <div class="validated-input-wrap">
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required data-email-validation="preventive" aria-describedby="emailFeedback">
                        <span class="valid-icon" aria-hidden="true">✓</span>
                    </div>
                    <div id="emailFeedback" class="input-feedback" data-email-feedback="email"></div>

                    <label for="password">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>

                    <label for="math_answer">Verifica que no eres un bot: {{ $mathQuestion }} = ?</label>
                    <div class="math-field-wrap {{ session('math_failed') ? 'math-error' : '' }}">
                        <input id="math_answer" name="math_answer" type="number" inputmode="numeric" required aria-label="Resultado de la suma">
                        <span aria-hidden="true">!</span>
                    </div>

                    <button class="btn btn-primary btn-full" type="submit">Iniciar sesión</button>

                    @if ($errors->any())
                        <div class="auth-error">No fue posible iniciar sesión.</div>
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
