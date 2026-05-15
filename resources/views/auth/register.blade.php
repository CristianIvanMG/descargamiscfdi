@extends('layouts.app', [
    'title' => 'Crear cuenta | ContaPro',
    'metaDescription' => 'Crea tu cuenta en ContaPro para descargar y ordenar CFDI emitidos y recibidos del SAT.',
])

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="auth-grid">
                <div>
                    <span class="eyebrow">Cuenta privada</span>
                    <h1>Regístrate para probar ContaPro</h1>
                    <p>Solo necesitamos tu correo y contraseña. Dentro del perfil podrás agregar tus datos fiscales, clientes, RFC y preferencias cuando lo decidas.</p>
                </div>
                <form class="auth-card" action="{{ url('/dashboard') }}" method="get" aria-label="Registro ContaPro">
                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" value="{{ request('email') }}" placeholder="tu@despacho.com" autocomplete="email" required>

                    <label for="password">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>

                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>

                    <button class="btn btn-primary btn-full" type="submit">Crear cuenta</button>
                    <p>Después podrás completar tu perfil fiscal desde el panel privado.</p>
                </form>
            </div>
        </div>
    </section>
@endsection
