@extends('layouts.app', ['title' => __('app.dashboard.title')])

@section('content')
    <section class="app-shell py-4" x-data="contadorMxDashboard('{{ route('api.dashboard.metricas') }}')" x-init="load()">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">{{ __('app.dashboard.heading') }}</h1>
                    <p class="text-secondary mb-0">{{ __('app.dashboard.subtitle') }}</p>
                </div>
                <a class="btn btn-primary" href="{{ route('descargas.create') }}">{{ __('app.dashboard.new_download') }}</a>
            </div>

            <div class="row g-3 mb-4">
                @foreach ([
                    ['key' => 'cfdi_emitidos', 'label' => __('app.dashboard.issued')],
                    ['key' => 'cfdi_recibidos', 'label' => __('app.dashboard.received')],
                    ['key' => 'iva_trasladado', 'label' => __('app.dashboard.vat_charged')],
                    ['key' => 'iva_acreditable', 'label' => __('app.dashboard.vat_creditable')],
                ] as $item)
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="metric-card">
                            <span class="metric-label">{{ $item['label'] }}</span>
                            <strong x-text="metricas.{{ $item['key'] }}"></strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="panel">
                <h2 class="h5 mb-3">{{ __('app.dashboard.chart_title') }}</h2>
                <div class="chart-box">
                    <canvas id="dashboardChart" aria-label="{{ __('app.dashboard.chart_label') }}" role="img"></canvas>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('dashboardChart');

            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: ['{{ __('app.dashboard.issued') }}', '{{ __('app.dashboard.received') }}'],
                    datasets: [{
                        label: '{{ __('app.nav.cfdi') }}',
                        data: [0, 0],
                        backgroundColor: ['#1d4ed8', '#059669']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endpush
