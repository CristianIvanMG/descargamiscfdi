@extends('layouts.app', ['title' => __('app.suscripcion.title')])

@section('content')
    <section class="app-shell py-4">
        <div class="container">
            <div class="mb-4">
                <h1 class="h3 fw-bold mb-1">Planes ContaPro</h1>
                <p class="text-secondary mb-0">Puedes usar CFDI gratis. Pagar vale la pena cuando necesitas multiples RFC, clientes e historial.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            <div class="row g-3">
                @foreach ($planes as $plan)
                    <div class="col-12 col-md-3">
                        <form class="panel h-100" method="post" action="{{ url('/suscripcion/checkout') }}">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $plan['clave'] }}">
                            <h2 class="h5">{{ $plan['nombre'] }}</h2>
                            <p class="display-6 fw-bold">${{ number_format($plan['precio']) }}</p>

                            @if ($plan['clave'] === 'gratis')
                                <p class="text-secondary">CFDI de tu propio RFC, solicitudes por mes dentro del año en curso y exportacion a Excel.</p>
                                <a class="btn btn-outline-primary w-100" href="{{ url('/cfdi') }}">Usar gratis</a>
                            @elseif ($plan['clave'] === 'anual_completo')
                                <p class="text-secondary">Multi RFC, clientes, descargas avanzadas, exportacion e historial completo para despachos.</p>
                                <button class="btn btn-primary w-100" type="submit">Pagar con Mercado Pago</button>
                            @else
                                <p class="text-secondary">Funciones premium para contadores: multiples RFC, clientes y mayor volumen operativo.</p>
                                <button class="btn btn-primary w-100" type="submit">Pagar con Mercado Pago</button>
                            @endif
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
