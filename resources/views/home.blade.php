@extends('layouts.app', ['title' => __('app.home.title')])

@section('content')
    <section class="hero-band">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-6">
                    <p class="trust-label mb-3">{{ __('app.home.phase') }}</p>
                    <h1 class="hero-title mb-3">{{ __('app.home.heading') }}</h1>
                    <p class="hero-copy mb-4">{{ __('app.home.subtitle') }}</p>
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <a class="btn btn-primary btn-lg" href="{{ url('/descargas/nueva') }}">{{ __('app.home.primary_cta') }}</a>
                        <a class="btn btn-outline-primary btn-lg" href="{{ url('/dashboard') }}">{{ __('app.home.secondary_cta') }}</a>
                    </div>
                    <div class="trust-row mt-4" aria-label="{{ __('app.home.trust_label') }}">
                        <span>{{ __('app.home.trust_web') }}</span>
                        <span>{{ __('app.home.trust_key') }}</span>
                        <span>{{ __('app.home.trust_sat') }}</span>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="product-preview" aria-label="{{ __('app.home.preview_label') }}">
                        <div class="preview-toolbar">
                            <span></span>
                            <span></span>
                            <span></span>
                            <strong>{{ __('app.home.preview_title') }}</strong>
                        </div>
                        <div class="preview-grid">
                            <div class="preview-main">
                                <div class="preview-kpi">
                                    <span>{{ __('app.dashboard.received') }}</span>
                                    <strong>1,248</strong>
                                </div>
                                <div class="preview-kpi green">
                                    <span>{{ __('app.dashboard.vat_creditable') }}</span>
                                    <strong>$84,320</strong>
                                </div>
                                <div class="preview-bars">
                                    <i style="height: 48%"></i>
                                    <i style="height: 72%"></i>
                                    <i style="height: 56%"></i>
                                    <i style="height: 86%"></i>
                                    <i style="height: 64%"></i>
                                </div>
                            </div>
                            <div class="preview-side">
                                <div class="secure-box">
                                    <span class="status-dot" aria-hidden="true"></span>
                                    <strong>{{ __('app.home.secure_title') }}</strong>
                                    <p>{{ __('app.home.secure_body') }}</p>
                                </div>
                                <div class="download-box">
                                    <span>{{ __('app.home.download_progress') }}</span>
                                    <div class="progress" role="progressbar" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: 72%"></div>
                                    </div>
                                    <strong>72%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4 py-lg-5">
        <div class="container">
            <div class="section-heading mb-4">
                <h2>{{ __('app.home.simple_title') }}</h2>
                <p>{{ __('app.home.simple_subtitle') }}</p>
            </div>
            <div class="row g-3">
                @foreach (__('app.home.steps') as $index => $step)
                    <div class="col-12 col-md-4">
                        <div class="step-card h-100">
                            <span class="step-number">{{ $index + 1 }}</span>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['body'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-4 py-lg-5 bg-white">
        <div class="container">
            <div class="section-heading mb-4">
                <h2>{{ __('app.home.advantage_title') }}</h2>
                <p>{{ __('app.home.advantage_subtitle') }}</p>
            </div>
            <div class="row g-3 mb-4">
                @foreach (__('app.home.advantages') as $advantage)
                    <div class="col-12 col-lg-4">
                        <div class="advantage-card h-100">
                            <span class="advantage-mark" aria-hidden="true"></span>
                            <h3>{{ $advantage['title'] }}</h3>
                            <p>{{ $advantage['body'] }}</p>
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

    <section class="final-cta py-4 py-lg-5">
        <div class="container">
            <div class="final-cta-inner">
                <h2>{{ __('app.home.final_title') }}</h2>
                <p>{{ __('app.home.final_body') }}</p>
                <a class="btn btn-light btn-lg" href="{{ url('/descargas/nueva') }}">{{ __('app.home.primary_cta') }}</a>
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
