@extends('layouts.app', [
    'title' => 'Perfil de negocio | ContaPro',
    'hideNav' => true,
])

@section('content')
    <div class="app-workspace">
        <aside class="app-sidebar" aria-label="Menú fiscal">
            <a class="workspace-brand" href="{{ url('/dashboard') }}">
                <span>CP</span>
                <strong>ContaPro</strong>
            </a>
            <nav>
                <a href="{{ url('/dashboard') }}"><span>▦</span>Dashboard</a>
                <a href="{{ url('/cfdi?tipo=emitidos') }}"><span>▤</span>CFDI Emitidos</a>
                <a href="{{ url('/cfdi?tipo=recibidos') }}"><span>▥</span>CFDI Recibidos</a>
                <a href="{{ url('/descargas/nueva') }}"><span>⇩</span>Descarga masiva</a>
                <a href="{{ url('/cfdi') }}"><span>▧</span>Reportes</a>
                <a href="{{ url('/dashboard') }}"><span>▣</span>Declaraciones</a>
                <a class="active" href="{{ url('/perfil') }}"><span>◫</span>Perfil / Configuración</a>
            </nav>
        </aside>

        <main class="workspace-main">
            <header class="workspace-topbar">
                <div>
                    <h1>Perfil de negocio</h1>
                    <p>Configura tu entorno de trabajo para empezar con CFDI.</p>
                </div>
            </header>

            <section class="workspace-panel profile-panel">
                <div class="panel-header">
                    <div>
                        <h2>Completa tu perfil para empezar a trabajar con CFDI.</h2>
                        <p>Estos datos ayudan a personalizar el flujo de descargas, reportes y declaraciones.</p>
                    </div>
                </div>
                <form class="profile-form" action="{{ url('/dashboard') }}" method="get">
                    <div>
                        <label for="business_name">Nombre del contador o despacho</label>
                        <input id="business_name" name="business_name" type="text" placeholder="Ej. Despacho Hernández y Asociados" required>
                    </div>
                    <div>
                        <label for="rfc">RFC</label>
                        <input id="rfc" name="rfc" type="text" maxlength="13" placeholder="Opcional al inicio" data-rfc-mask aria-describedby="rfcFeedback">
                        <small id="rfcFeedback" class="rfc-feedback" data-rfc-feedback="rfc">Opcional al inicio. Formato: 12 o 13 caracteres.</small>
                    </div>
                    <div>
                        <label for="type">Tipo de usuario</label>
                        <select id="type" name="type" required>
                            <option>Contador independiente</option>
                            <option>Despacho contable</option>
                        </select>
                    </div>
                    <div>
                        <label for="email">Correo principal</label>
                        <input id="email" name="email" type="email" placeholder="correo@despacho.com" required>
                    </div>
                    <div>
                        <label for="country">Zona fiscal / país</label>
                        <select id="country" name="country" required>
                            <option>México</option>
                        </select>
                    </div>
                    <div class="profile-actions">
                        <button class="btn btn-primary" type="submit">Guardar y entrar al dashboard</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
@endsection
