@extends('layouts.app', [
    'title' => 'Dashboard fiscal | ContaPro',
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
                <a class="active" href="{{ url('/dashboard') }}"><span>▦</span>Dashboard</a>
                <a href="{{ url('/cfdi?tipo=emitidos') }}"><span>▤</span>CFDI Emitidos</a>
                <a href="{{ url('/cfdi?tipo=recibidos') }}"><span>▥</span>CFDI Recibidos</a>
                <a href="{{ url('/descargas/nueva') }}"><span>⇩</span>Descarga masiva</a>
                <a href="{{ url('/cfdi') }}"><span>▧</span>Reportes</a>
                <a href="{{ url('/dashboard') }}"><span>▣</span>Declaraciones</a>
                <a href="{{ url('/perfil') }}"><span>◫</span>Perfil / Configuración</a>
            </nav>
        </aside>

        <main class="workspace-main">
            <header class="workspace-topbar">
                <div>
                    <h1>Dashboard</h1>
                    <p>Resumen fiscal del mes actual</p>
                </div>
                <div class="workspace-user">
                    <span>C</span>
                    <div>
                        <strong>Contador</strong>
                        <small>Cuenta pendiente de perfil</small>
                    </div>
                </div>
            </header>

            <section class="orientation-box">
                <div>
                    <strong>Empieza descargando los CFDI de tu cliente para ver información aquí.</strong>
                    <p>Cuando agregues un RFC y solicites la descarga masiva, este panel mostrará emitidos, recibidos, ingresos, gastos e IVA.</p>
                </div>
                <a class="btn btn-primary" href="{{ url('/descargas/nueva') }}">Nueva descarga</a>
            </section>

            <section class="fiscal-kpi-grid" aria-label="Indicadores fiscales">
                @foreach ([
                    ['label' => 'Total CFDI emitidos', 'value' => '0', 'note' => 'Mes actual'],
                    ['label' => 'Total CFDI recibidos', 'value' => '0', 'note' => 'Mes actual'],
                    ['label' => 'Ingresos acumulados', 'value' => '$0.00', 'note' => 'Según CFDI emitidos'],
                    ['label' => 'Gastos deducibles detectados', 'value' => '$0.00', 'note' => 'Según CFDI recibidos'],
                    ['label' => 'IVA trasladado / acreditable', 'value' => '$0.00 / $0.00', 'note' => 'Base para revisión'],
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
                    <h2>Actividad reciente de CFDI</h2>
                    <a href="{{ url('/cfdi') }}">Ver todos</a>
                </div>
                <div class="table-responsive">
                    <table class="fiscal-table">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>RFC</th>
                            <th>Concepto</th>
                            <th class="text-end">Total</th>
                            <th>Estatus</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ([
                            ['fecha' => 'Pendiente', 'tipo' => 'Emitido', 'rfc' => 'Sin RFC', 'concepto' => 'Descarga inicial pendiente', 'total' => '$0.00', 'estatus' => 'Sin datos'],
                            ['fecha' => 'Pendiente', 'tipo' => 'Recibido', 'rfc' => 'Sin RFC', 'concepto' => 'Agrega un cliente para iniciar', 'total' => '$0.00', 'estatus' => 'Sin datos'],
                        ] as $row)
                            <tr>
                                <td>{{ $row['fecha'] }}</td>
                                <td>{{ $row['tipo'] }}</td>
                                <td>{{ $row['rfc'] }}</td>
                                <td>{{ $row['concepto'] }}</td>
                                <td class="text-end">{{ $row['total'] }}</td>
                                <td><span class="status-pill">{{ $row['estatus'] }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
@endsection
