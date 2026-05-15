@extends('layouts.app', ['title' => __('app.home.title')])

@section('content')
    <section class="app-shell py-4 py-lg-5">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <p class="text-uppercase text-primary fw-semibold small mb-2">{{ __('app.home.phase') }}</p>
                    <h1 class="h2 fw-bold mb-2">{{ __('app.home.heading') }}</h1>
                    <p class="text-secondary mb-0">{{ __('app.home.subtitle') }}</p>
                </div>
                <div class="d-flex align-items-start">
                    <span class="badge text-bg-success rounded-pill px-3 py-2">{{ __('app.home.status_ready') }}</span>
                </div>
            </div>

            <div class="row g-3 mb-4">
                @foreach ([
                    ['label' => __('app.stack.backend'), 'value' => 'Laravel 11 + PHP 8.2'],
                    ['label' => __('app.stack.database'), 'value' => 'MySQL 8'],
                    ['label' => __('app.stack.queue'), 'value' => __('app.stack.queue_value')],
                    ['label' => __('app.stack.storage'), 'value' => __('app.stack.storage_value')],
                ] as $item)
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="metric-card h-100">
                            <span class="metric-label">{{ $item['label'] }}</span>
                            <strong>{{ $item['value'] }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row g-4">
                <div class="col-12 col-xl-7">
                    <div class="panel h-100" x-data="{ showDetails: true }">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                            <h2 class="h5 mb-0">{{ __('app.home.checklist_title') }}</h2>
                            <button class="btn btn-outline-primary btn-sm" type="button" x-on:click="showDetails = !showDetails" :aria-expanded="showDetails.toString()">
                                <span x-text="showDetails ? '{{ __('app.actions.hide') }}' : '{{ __('app.actions.show') }}'"></span>
                            </button>
                        </div>
                        <div x-show="showDetails" x-transition>
                            <div class="list-group list-group-flush">
                                @foreach (__('app.home.checklist') as $check)
                                    <div class="list-group-item px-0 d-flex gap-3 align-items-start">
                                        <span class="status-dot mt-1" aria-hidden="true"></span>
                                        <div>
                                            <strong class="d-block">{{ $check['title'] }}</strong>
                                            <span class="text-secondary">{{ $check['body'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-5">
                    <div class="panel h-100">
                        <h2 class="h5 mb-3">{{ __('app.home.chart_title') }}</h2>
                        <div class="chart-box">
                            <canvas id="stackChart" aria-label="{{ __('app.home.chart_label') }}" role="img"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('stackChart');

            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: ['{{ __('app.chart.laravel') }}', '{{ __('app.chart.mysql') }}', '{{ __('app.chart.queue') }}', '{{ __('app.chart.storage') }}'],
                    datasets: [{
                        data: [35, 25, 20, 20],
                        backgroundColor: ['#1d4ed8', '#059669', '#d97706', '#7c3aed'],
                        borderColor: '#ffffff',
                        borderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
@endpush
