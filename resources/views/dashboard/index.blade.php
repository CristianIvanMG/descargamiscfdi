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
            <form class="sidebar-logout" action="{{ url('/logout') }}" method="post">
                @csrf
                <button type="submit"><span>↩</span>Cerrar sesión</button>
            </form>
        </aside>

        <main class="workspace-main">
            <header class="workspace-topbar">
                <div>
                    <h1>Dashboard</h1>
                    <p>Resumen fiscal del mes actual</p>
                </div>
                <div class="workspace-actions">
                    <div class="workspace-user">
                        <span>{{ mb_substr(auth()->user()?->name ?? 'C', 0, 1) }}</span>
                        <div>
                            <strong>{{ auth()->user()?->name ?? 'Contador' }}</strong>
                            <small>{{ auth()->user()?->email ?? 'Cuenta verificada' }}</small>
                        </div>
                    </div>
                </div>
            </header>

            @if (session('status'))
                <section class="orientation-box">
                    <div>
                        <strong>{{ session('status') }}</strong>
                        <p>Ya puedes iniciar tus procesos de descarga y análisis CFDI.</p>
                    </div>
                </section>
            @endif

            <section class="orientation-box">
                <div>
                    <strong>Resumen general</strong>
                    <p>RFC activo: <b>{{ $profile?->rfc ?? 'Pendiente' }}</b> · Periodo: <b>{{ ucfirst($periodLabel) }}</b> · Tipo: <b>{{ $profile?->user_type ?? 'Pendiente' }}</b></p>
                </div>
                <a class="btn btn-primary" href="{{ url('/descargas/nueva') }}">Descargar CFDI</a>
            </section>

            <section class="fiscal-kpi-grid" aria-label="Indicadores fiscales">
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
                        <h2>Acciones rápidas</h2>
                        <p>Inicia las tareas frecuentes sin buscar entre módulos.</p>
                    </div>
                </div>
                <div class="quick-actions-grid">
                    <a href="{{ url('/descargas/nueva') }}">
                        <strong>Descargar CFDI</strong>
                        <span>{{ $canUseMultipleRfcs ? 'Usa tu RFC o clientes registrados.' : 'Modo gratuito: RFC de tu perfil.' }}</span>
                    </a>
                    <a href="{{ url('/dashboard') }}">
                        <strong>Cambiar periodo</strong>
                        <span>Próximamente: filtros por mes y ejercicio fiscal.</span>
                    </a>
                    <a href="{{ url('/cfdi') }}">
                        <strong>Ir a reportes</strong>
                        <span>Revisa emitidos, recibidos, montos e IVA.</span>
                    </a>
                </div>
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
