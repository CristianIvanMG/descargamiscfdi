@extends('layouts.app', [
    'title' => 'Planes CFDI | ContaPro',
])

@section('content')
    @php
        $currentPlan = $currentSubscription?->plan;
    @endphp

    <section class="pricing-hero">
        <div class="pricing-container">
            <div class="pricing-eyebrow">Billing seguro con Mercado Pago</div>
            <h1>Elige el plan para trabajar CFDI sin friccion</h1>
            <p>Empieza gratis con tu RFC. Cuando necesites clientes, multiples RFC e historial, actualiza en un checkout seguro.</p>
        </div>
    </section>

    <section class="pricing-section">
        <div class="pricing-container">
            @if (session('status'))
                <div class="billing-alert">{{ session('status') }}</div>
            @endif

            <div class="pricing-grid">
                @foreach ($planes as $plan)
                    @php
                        $isCurrent = $currentPlan === $plan['clave'] || ($plan['clave'] === 'gratis' && ! $currentPlan);
                        $isPaid = $plan['clave'] !== 'gratis';
                    @endphp
                    <article @class(['pricing-card', 'is-popular' => $plan['popular'], 'is-current' => $isCurrent])>
                        @if ($plan['popular'])
                            <span class="pricing-badge">Mas popular</span>
                        @endif
                        @if ($isCurrent)
                            <span class="pricing-current">Plan actual</span>
                        @endif

                        <div class="pricing-card-head">
                            <h2>{{ $plan['nombre'] }}</h2>
                            <p>{{ $plan['descripcion'] }}</p>
                        </div>

                        <div class="pricing-price">
                            <span>${{ number_format($plan['precio']) }}</span>
                            <small>{{ $plan['periodo'] }}</small>
                        </div>

                        <ul class="pricing-features">
                            @foreach ($plan['beneficios'] as $beneficio)
                                <li>{{ $beneficio }}</li>
                            @endforeach
                        </ul>

                        @if ($isCurrent)
                            <button class="pricing-cta is-disabled" type="button" disabled>Plan actual</button>
                        @elseif ($isPaid && auth()->check())
                            <form method="post" action="{{ url('/subscriptions/create') }}">
                                @csrf
                                <input type="hidden" name="plan" value="{{ $plan['clave'] }}">
                                <button class="pricing-cta" type="submit">Suscribirme</button>
                            </form>
                        @elseif ($isPaid)
                            <a class="pricing-cta" href="{{ url('/login') }}">Iniciar sesion</a>
                        @else
                            <a class="pricing-cta secondary" href="{{ auth()->check() ? url('/cfdi') : url('/registro') }}">Empezar ahora</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
