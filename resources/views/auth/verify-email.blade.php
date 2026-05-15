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
                <h1>Te enviamos un correo para confirmar tu cuenta antes de continuar.</h1>
                <p>Revisa tu bandeja de entrada y confirma tu correo. Después podrás configurar tu perfil y entrar al dashboard.</p>
                <a class="btn btn-primary" href="{{ url('/registro/confirmado') }}">Simular confirmación</a>
            </div>
        </div>
    </section>
@endsection
