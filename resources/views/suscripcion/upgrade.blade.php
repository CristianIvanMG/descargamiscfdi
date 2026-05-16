@extends('layouts.app', [
    'title' => 'Actualizar plan | ContaPro',
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
                <a href="{{ url('/perfil') }}"><span>◫</span>Perfil / Configuración</a>
                <a class="active" href="{{ url('/suscripcion/upgrade') }}"><span>▧</span>Membresía</a>
            </nav>
            <form class="sidebar-logout" action="{{ url('/logout') }}" method="post">
                @csrf
                <button type="submit"><span>↩</span>Cerrar sesión</button>
            </form>
        </aside>

        <main class="workspace-main">
            <header class="workspace-topbar">
                <div>
                    <h1>Membresía requerida</h1>
                    <p>El plan gratuito está disponible solo para persona física.</p>
                </div>
            </header>

            <section class="workspace-panel profile-panel">
                <div class="panel-header">
                    <div>
                        <h2>Activa un plan para herramientas avanzadas</h2>
                        <p>Contadores independientes y despachos requieren plan para descargas avanzadas, múltiples clientes, rangos amplios y reportes.</p>
                    </div>
                </div>
                @if (session('status'))
                    <div class="auth-success">{{ session('status') }}</div>
                @endif
                <a class="btn btn-primary" href="{{ url('/suscripcion/planes') }}">Ver planes</a>
                <a class="btn btn-outline-primary ms-2" href="{{ url('/dashboard') }}">Volver al dashboard</a>
            </section>
        </main>
    </div>
@endsection
