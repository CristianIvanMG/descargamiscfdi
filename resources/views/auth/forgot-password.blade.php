@extends('layouts.app', [
    'title' => 'Recuperar contraseña | ContaPro',
    'hideNav' => true,
])

@section('content')
    <section class="auth-screen">
        <div class="auth-shell narrow-auth">
            <a class="auth-brand" href="{{ url('/') }}">ContaPro</a>
            <form class="auth-card auth-single-card" action="{{ url('/login') }}" method="get">
                <span class="eyebrow">Recuperación</span>
                <h1>Recupera tu acceso</h1>
                <p>Ingresa tu correo y te enviaremos instrucciones para restablecer tu contraseña.</p>
                <label for="email">Correo electrónico</label>
                <input id="email" name="email" type="email" autocomplete="email" required>
                <button class="btn btn-primary btn-full" type="submit">Enviar instrucciones</button>
                <a href="{{ url('/login') }}">Volver al login</a>
            </form>
        </div>
    </section>
@endsection
