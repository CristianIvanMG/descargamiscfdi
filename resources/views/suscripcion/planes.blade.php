@extends('layouts.app', ['title' => __('app.suscripcion.title')])

@section('content')
    <section class="app-shell py-4">
        <div class="container">
            <div class="mb-4">
                <h1 class="h3 fw-bold mb-1">{{ __('app.suscripcion.heading') }}</h1>
                <p class="text-secondary mb-0">{{ __('app.suscripcion.subtitle') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            <div class="row g-3">
                @foreach ($planes as $plan)
                    <div class="col-12 col-md-4">
                        <form class="panel h-100" method="post" action="{{ url('/suscripcion/checkout') }}">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $plan['clave'] }}">
                            <h2 class="h5">{{ $plan['nombre'] }}</h2>
                            <p class="display-6 fw-bold">${{ number_format($plan['precio']) }}</p>
                            <p class="text-secondary">Clientes, múltiples RFC, rangos avanzados, historial y exportación a Excel.</p>
                            <button class="btn btn-primary w-100" type="submit">{{ __('app.suscripcion.choose') }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
