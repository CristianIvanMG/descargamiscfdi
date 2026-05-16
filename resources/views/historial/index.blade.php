@extends('layouts.app', [
    'title' => 'Historial SAT | ContaPro',
    'hideNav' => true,
])

@section('content')
    <div class="app-workspace">
        <aside class="app-sidebar" aria-label="Menu fiscal">
            <a class="workspace-brand" href="{{ url('/dashboard') }}">
                <span>CP</span>
                <strong>ContaPro</strong>
            </a>
            <nav>
                <a href="{{ url('/dashboard') }}"><span>▦</span>Inicio</a>
                <a href="{{ url('/cfdi') }}"><span>▤</span>CFDI</a>
                <a href="{{ url('/descargas/nueva') }}"><span>⇩</span>Descarga masiva</a>
                <a class="active" href="{{ url('/historial') }}"><span>▧</span>Historial</a>
                <a href="{{ url('/perfil') }}"><span>◫</span>Perfil / Configuracion</a>
            </nav>
            <form class="sidebar-logout" action="{{ url('/logout') }}" method="post">
                @csrf
                <button type="submit"><span>↩</span>Cerrar sesion</button>
            </form>
        </aside>

        <main class="workspace-main">
            <header class="workspace-topbar">
                <div>
                    <h1>Historial SAT</h1>
                    <p>Solicitudes guardadas para despachos con plan anual completo.</p>
                </div>
            </header>

            <section class="workspace-panel">
                <div class="panel-header">
                    <div>
                        <h2>Solicitudes de descarga</h2>
                        <p>Consulta, audita y evita duplicar solicitudes por cliente o RFC.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="fiscal-table">
                        <thead>
                        <tr>
                            <th>Cliente / RFC</th>
                            <th>Tipo</th>
                            <th>Periodo</th>
                            <th>Estado</th>
                            <th>RequestId SAT</th>
                            <th class="text-end">CFDI</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($history as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->razon_social ?? 'RFC consultado' }}</strong><br>
                                    <small>{{ $item->rfc }}</small>
                                </td>
                                <td>{{ ucfirst($item->tipo) }}</td>
                                <td>{{ $item->fecha_inicio }} a {{ $item->fecha_fin }}</td>
                                <td><span class="cfdi-status active">{{ $item->estado }}</span></td>
                                <td>{{ $item->solicitud_id ?? 'Pendiente' }}</td>
                                <td class="text-end">{{ number_format((int) $item->total_cfdi) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">Todavia no hay solicitudes guardadas.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if (method_exists($history, 'links'))
                    <div class="mt-3">{{ $history->links() }}</div>
                @endif
            </section>
        </main>
    </div>
@endsection
