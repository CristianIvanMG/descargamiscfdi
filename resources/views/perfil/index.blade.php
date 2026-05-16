@extends('layouts.app', [
    'title' => 'Perfil de negocio | ContaPro',
    'hideNav' => true,
])

@section('content')
    @php
        $isComplete = $profile->isComplete();
    @endphp

    <div class="app-workspace">
        <aside class="app-sidebar" aria-label="Menú fiscal">
            <a class="workspace-brand" href="{{ $isComplete ? url('/dashboard') : url('/perfil') }}">
                <span>CP</span>
                <strong>ContaPro</strong>
            </a>

            @if ($isComplete)
                <nav>
                    <a href="{{ url('/dashboard') }}"><span>▦</span>Dashboard</a>
                    <a href="{{ url('/cfdi?tipo=emitidos') }}"><span>▤</span>CFDI Emitidos</a>
                    <a href="{{ url('/cfdi?tipo=recibidos') }}"><span>▥</span>CFDI Recibidos</a>
                    <a href="{{ url('/descargas/nueva') }}"><span>⇩</span>Descarga masiva</a>
                    <a href="{{ url('/cfdi') }}"><span>▧</span>Reportes</a>
                    <a href="{{ url('/dashboard') }}"><span>▣</span>Declaraciones</a>
                    <a class="active" href="{{ url('/perfil') }}"><span>◫</span>Perfil / Configuración</a>
                </nav>
            @else
                <div class="sidebar-locked">
                    <strong>Configuración requerida</strong>
                    <p>Completa tu perfil para habilitar el dashboard y las herramientas CFDI.</p>
                </div>
            @endif

            <form class="sidebar-logout" action="{{ url('/logout') }}" method="post">
                @csrf
                <button type="submit"><span>↩</span>Cerrar sesión</button>
            </form>
        </aside>

        <main class="workspace-main">
            <header class="workspace-topbar">
                <div>
                    <h1>Perfil de negocio</h1>
                    <p>Completa tu entorno de trabajo para empezar con CFDI.</p>
                </div>
            </header>

            <section class="workspace-panel profile-panel">
                <div class="panel-header">
                    <div>
                        <h2>Completa tu perfil para empezar a trabajar con CFDI.</h2>
                        <p>Todos los campos son obligatorios. Esta configuración protege el flujo fiscal y evita datos incompletos.</p>
                    </div>
                </div>

                @if (session('status'))
                    <div class="auth-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-error">Ocurrió un problema al cargar tu información. Intenta nuevamente.</div>
                @endif

                <form class="profile-form" action="{{ url('/perfil') }}" method="post">
                    @csrf
                    <div>
                        <label for="business_name">Nombre del contador o despacho</label>
                        <input id="business_name" name="business_name" type="text" value="{{ old('business_name', $profile->business_name) }}" placeholder="Ej. DESPACHO HERNÁNDEZ Y ASOCIADOS" required data-name-mask>
                    </div>
                    <div>
                        <label for="rfc">RFC</label>
                        <input id="rfc" name="rfc" type="text" value="{{ old('rfc', $profile->rfc) }}" maxlength="13" placeholder="RFC CON HOMOCLAVE" required data-rfc-mask aria-describedby="rfcFeedback">
                        <small id="rfcFeedback" class="rfc-feedback" data-rfc-feedback="rfc">Formato: 12 o 13 caracteres con homoclave.</small>
                    </div>
                    <div>
                        <label for="type">Tipo de usuario</label>
                        <select id="type" name="user_type" required>
                            <option value="">Selecciona una opción</option>
                            <option @selected(old('user_type', $profile->user_type) === 'Persona física')>Persona física</option>
                            <option @selected(old('user_type', $profile->user_type) === 'Contador independiente')>Contador independiente</option>
                            <option @selected(old('user_type', $profile->user_type) === 'Despacho contable')>Despacho contable</option>
                        </select>
                    </div>
                    <div>
                        <label for="primary_email">Correo principal</label>
                        <div class="validated-input-wrap">
                            <input id="primary_email" name="primary_email" type="email" value="{{ old('primary_email', $profile->primary_email) }}" placeholder="correo@despacho.com" required data-email-validation="strict" aria-describedby="profileEmailFeedback">
                            <span class="valid-icon" aria-hidden="true">✓</span>
                        </div>
                        <small id="profileEmailFeedback" class="input-feedback" data-email-feedback="primary_email"></small>
                    </div>
                    <div>
                        <label for="country">Zona fiscal / Estado</label>
                        <select id="country" name="country" required data-state-combobox>
                            <option value="">Selecciona un estado</option>
                            @foreach ($estados as $estado)
                                <option @selected(old('country', $profile->country) === $estado)>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="profile-actions">
                        <button class="btn btn-primary" type="submit">{{ $isComplete ? 'Guardar' : 'Guardar y entrar al dashboard' }}</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
@endsection
