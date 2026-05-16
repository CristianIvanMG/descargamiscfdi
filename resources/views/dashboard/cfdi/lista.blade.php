@extends('layouts.app', [
    'title' => 'CFDI | ContaPro',
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
                <a href="{{ url('/dashboard') }}"><span>▦</span>Inicio</a>
                <a class="active" href="{{ url('/cfdi') }}"><span>▤</span>CFDI</a>
                <a href="{{ url('/descargas/nueva') }}"><span>⇩</span>Descarga masiva</a>
                <a href="{{ url('/cfdi') }}"><span>▧</span>Reportes</a>
                <a href="{{ url('/dashboard') }}"><span>▣</span>Declaraciones</a>
                <a href="{{ url('/perfil') }}"><span>◫</span>Perfil / Configuración</a>
            </nav>
            <form class="sidebar-logout" action="{{ url('/logout') }}" method="post">
                @csrf
                <button type="submit"><span>↩</span>Cerrar sesión</button>
            </form>
        </aside>

        <main class="workspace-main">
            <header class="workspace-topbar">
                <div>
                    <h1>CFDI</h1>
                    <p>Panel operativo para emitidos, recibidos, montos e IVA.</p>
                </div>
                <div class="workspace-actions">
                    <a class="btn btn-primary" href="{{ url('/descargas/nueva') }}">Descargar CFDI</a>
                </div>
            </header>

            <section class="orientation-box">
                <div>
                    <strong>Resumen general</strong>
                    <p>Consulta y filtra CFDI emitidos y recibidos desde una sola vista.</p>
                </div>
            </section>

            <section class="fiscal-kpi-grid" aria-label="Indicadores CFDI">
                @foreach ([
                    ['label' => 'CFDI emitidos', 'value' => number_format($metrics['emitidos_count']), 'note' => 'Total de comprobantes del periodo'],
                    ['label' => 'Monto emitido acumulado', 'value' => '$'.number_format($metrics['emitidos_total'], 2), 'note' => 'Ingresos detectados por CFDI'],
                    ['label' => 'IVA trasladado', 'value' => '$'.number_format($metrics['iva_trasladado'], 2), 'note' => 'Base para declaración'],
                    ['label' => 'CFDI recibidos', 'value' => number_format($metrics['recibidos_count']), 'note' => 'Comprobantes recibidos del periodo'],
                    ['label' => 'Gastos detectados', 'value' => '$'.number_format($metrics['recibidos_total'], 2), 'note' => 'Egresos deducibles por revisar'],
                    ['label' => 'IVA acreditable', 'value' => '$'.number_format($metrics['iva_acreditable'], 2), 'note' => 'Base para revisión fiscal'],
                ] as $metric)
                    <article class="fiscal-kpi">
                        <span>{{ $metric['label'] }}</span>
                        <strong>{{ $metric['value'] }}</strong>
                        <small>{{ $metric['note'] }}</small>
                    </article>
                @endforeach
            </section>

            <section class="workspace-panel">
                <div class="panel-header">
                    <div>
                        <h2>Filtros CFDI</h2>
                        <p>La diferenciación entre emitidos y recibidos se controla aquí.</p>
                    </div>
                </div>

                <div class="cfdi-filter-tabs" role="tablist" aria-label="Filtros CFDI">
                    <a @class(['active' => $filters['tipo'] === 'todos']) href="{{ url('/cfdi?tipo=todos') }}">Todos</a>
                    <a @class(['active' => $filters['tipo'] === 'emitidos']) href="{{ url('/cfdi?tipo=emitidos') }}">Emitidos</a>
                    <a @class(['active' => $filters['tipo'] === 'recibidos']) href="{{ url('/cfdi?tipo=recibidos') }}">Recibidos</a>
                </div>

                <form class="cfdi-filter-panel" method="get" action="{{ url('/cfdi') }}">
                    <div>
                        <label for="tipo">Tipo</label>
                        <select id="tipo" name="tipo">
                            <option value="todos" @selected($filters['tipo'] === 'todos')>Todos</option>
                            <option value="emitidos" @selected($filters['tipo'] === 'emitidos')>Emitidos</option>
                            <option value="recibidos" @selected($filters['tipo'] === 'recibidos')>Recibidos</option>
                        </select>
                    </div>
                    <div>
                        <label for="fecha_inicio">Fecha inicial</label>
                        <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ $filters['fecha_inicio'] }}">
                    </div>
                    <div>
                        <label for="fecha_fin">Fecha final</label>
                        <input id="fecha_fin" name="fecha_fin" type="date" value="{{ $filters['fecha_fin'] }}">
                    </div>
                    <div>
                        <label for="rfc">RFC</label>
                        <input id="rfc" name="rfc" value="{{ $filters['rfc'] }}" placeholder="Filtrar por RFC" data-rfc-mask>
                    </div>
                    <button class="btn btn-primary" type="submit">Aplicar filtros</button>
                </form>
            </section>

            <section class="workspace-panel">
                <div class="panel-header">
                    <h2>Comprobantes CFDI</h2>
                    <a href="{{ url('/descargas/nueva') }}">Nueva descarga</a>
                </div>
                <div class="table-responsive">
                    <table class="fiscal-table">
                        <thead>
                        <tr>
                            <th>UUID</th>
                            <th>Emisor</th>
                            <th>Receptor</th>
                            <th class="text-end">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-4">{{ __('app.cfdi.empty') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
@endsection
