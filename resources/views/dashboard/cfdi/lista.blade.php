@extends('layouts.app', ['title' => __('app.cfdi.title')])

@section('content')
    <section class="app-shell py-4">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">CFDI</h1>
                    <p class="text-secondary mb-0">Consulta, filtra y descarga CFDI emitidos y recibidos desde una sola sección.</p>
                </div>
                <a class="btn btn-primary" href="{{ url('/descargas/nueva') }}">{{ __('app.dashboard.new_download') }}</a>
            </div>

            <div class="cfdi-filter-tabs" role="tablist" aria-label="Filtros CFDI">
                <a @class(['active' => $filters['tipo'] === 'todos']) href="{{ url('/cfdi?tipo=todos') }}">Todos</a>
                <a @class(['active' => $filters['tipo'] === 'emitidos']) href="{{ url('/cfdi?tipo=emitidos') }}">Emitidos</a>
                <a @class(['active' => $filters['tipo'] === 'recibidos']) href="{{ url('/cfdi?tipo=recibidos') }}">Recibidos</a>
            </div>

            <section class="fiscal-kpi-grid cfdi-kpi-grid" aria-label="Indicadores CFDI">
                @foreach ([
                    ['label' => 'CFDI emitidos', 'value' => number_format($metrics['emitidos_count']), 'note' => 'Total de comprobantes emitidos'],
                    ['label' => 'Monto emitido', 'value' => '$'.number_format($metrics['emitidos_total'], 2), 'note' => 'Ingresos acumulados'],
                    ['label' => 'IVA trasladado', 'value' => '$'.number_format($metrics['iva_trasladado'], 2), 'note' => 'Base para declaración'],
                    ['label' => 'CFDI recibidos', 'value' => number_format($metrics['recibidos_count']), 'note' => 'Total de comprobantes recibidos'],
                    ['label' => 'Deducciones detectadas', 'value' => '$'.number_format($metrics['recibidos_total'], 2), 'note' => 'Gastos por revisar'],
                    ['label' => 'IVA acreditable', 'value' => '$'.number_format($metrics['iva_acreditable'], 2), 'note' => 'Base para revisión fiscal'],
                ] as $metric)
                    <article class="fiscal-kpi">
                        <span>{{ $metric['label'] }}</span>
                        <strong>{{ $metric['value'] }}</strong>
                        <small>{{ $metric['note'] }}</small>
                    </article>
                @endforeach
            </section>

            <form class="panel cfdi-filter-panel" method="get" action="{{ url('/cfdi') }}">
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

            <div class="panel">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>{{ __('app.cfdi.uuid') }}</th>
                            <th>{{ __('app.cfdi.issuer') }}</th>
                            <th>{{ __('app.cfdi.receiver') }}</th>
                            <th class="text-end">{{ __('app.cfdi.total') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-4">{{ __('app.cfdi.empty') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
