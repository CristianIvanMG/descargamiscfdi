@extends('layouts.app', [
    'title' => 'Cuenta confirmada | ContaPro',
    'hideNav' => true,
])

@section('content')
    <section class="auth-screen">
        <div class="auth-shell narrow-auth">
            <a class="auth-brand" href="{{ url('/') }}">ContaPro</a>
            <div class="auth-message-card">
                <span class="eyebrow">Cuenta activa</span>
                <h1>Cuenta confirmada. Ahora puedes configurar tu perfil.</h1>
                <p>Completa tu entorno de trabajo fiscal para empezar a descargar y analizar CFDI.</p>
                <a class="btn btn-primary" href="{{ url('/perfil') }}">Configurar perfil</a>
            </div>
        </div>
    </section>
@endsection
