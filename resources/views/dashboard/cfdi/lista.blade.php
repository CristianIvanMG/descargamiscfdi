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
                <a class="active" href="{{ url('/cfdi?tipo=todos') }}">Todos</a>
                <a href="{{ url('/cfdi?tipo=emitidos') }}">Emitidos</a>
                <a href="{{ url('/cfdi?tipo=recibidos') }}">Recibidos</a>
            </div>

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
