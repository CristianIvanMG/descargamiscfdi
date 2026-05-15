@extends('layouts.app', [
    'title' => 'Crear cuenta gratis | ContaPro',
    'metaDescription' => 'Crea tu perfil privado en ContaPro para preparar descargas masivas de XML CFDI del SAT, administrar RFC y consultar tu dashboard fiscal.',
])

@section('content')
    <section class="final-register">
        <div class="container">
            <div class="final-register-grid">
                <div>
                    <span class="section-eyebrow">Perfil privado</span>
                    <h1 class="display-5 fw-bold">Crea tu cuenta ContaPro</h1>
                    <p>Este registro conecta con el flujo de fase 2: dashboard, RFC administrados, nueva descarga SAT, estado de jobs, listado CFDI y planes.</p>
                    <ul class="check-list">
                        <li>Panel privado para tus RFC.</li>
                        <li>Descarga de CFDI emitidos y recibidos.</li>
                        <li>Preparado para reportes y validaciones.</li>
                    </ul>
                </div>
                <form class="landing-card" action="{{ url('/dashboard') }}" method="get" aria-label="Registro ContaPro">
                    <h2>Datos iniciales</h2>
                    <p>En la siguiente fase se conectará autenticación real. Por ahora este flujo te lleva al panel.</p>
                    <label for="name">Nombre</label>
                    <input id="name" name="name" type="text" placeholder="Tu nombre" required>
                    <label for="email">Correo</label>
                    <input id="email" name="email" type="email" value="{{ request('email') }}" placeholder="tu@empresa.com" required>
                    <label for="rfc">RFC principal</label>
                    <input id="rfc" name="rfc" type="text" value="{{ request('rfc') }}" maxlength="13" placeholder="RFC a revisar" required>
                    <button class="btn btn-primary btn-full" type="submit">Entrar al dashboard</button>
                </form>
            </div>
        </div>
    </section>
@endsection
