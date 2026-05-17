@extends('layouts.app', [
    'title' => 'Suscripcion | ContaPro',
    'hideNav' => true,
])

@section('content')
    @php
        $currentPlan = $currentSubscription?->plan;
        $status = $currentSubscription ? 'Activo' : 'En prueba';
        $renewal = $currentSubscription?->periodo_fin ? \Carbon\Carbon::parse($currentSubscription->periodo_fin)->format('d/m/Y') : 'Sin renovacion';
        $canHistory = \App\Support\MembershipAccess::canAccessFullHistory(auth()->user());
        $hasPremium = \App\Support\MembershipAccess::hasActivePaidMembership(auth()->user());
        $premiumTooltip = 'Disponible en planes superiores';
    @endphp

    <div class="app-workspace">
        <aside class="app-sidebar" aria-label="Menu fiscal">
            <a class="workspace-brand" href="{{ url('/dashboard') }}">
                <span>CP</span>
                <strong>ContaPro</strong>
            </a>
            <nav>
                <a href="{{ url('/dashboard') }}"><span>▦</span>Inicio</a>
                <a href="{{ url('/cfdi') }}"><span>▤</span>CFDI</a>
                <a @class(['nav-locked' => ! $hasPremium]) href="{{ $hasPremium ? url('/descargas/nueva') : '#' }}" title="{{ ! $hasPremium ? $premiumTooltip : '' }}" aria-disabled="{{ ! $hasPremium ? 'true' : 'false' }}" @if(! $hasPremium) onclick="return false;" @endif><span>⇩</span>Descarga masiva</a>
                <a @class(['nav-locked' => ! $hasPremium]) href="{{ $hasPremium ? url('/cfdi') : '#' }}" title="{{ ! $hasPremium ? $premiumTooltip : '' }}" aria-disabled="{{ ! $hasPremium ? 'true' : 'false' }}" @if(! $hasPremium) onclick="return false;" @endif><span>▧</span>Reportes</a>
                <a @class(['nav-locked' => ! $hasPremium]) href="{{ $hasPremium ? url('/dashboard') : '#' }}" title="{{ ! $hasPremium ? $premiumTooltip : '' }}" aria-disabled="{{ ! $hasPremium ? 'true' : 'false' }}" @if(! $hasPremium) onclick="return false;" @endif><span>▣</span>Declaraciones</a>
                @if ($canHistory)
                    <a href="{{ url('/historial') }}"><span>▧</span>Historial</a>
                @endif
                <a href="{{ url('/perfil') }}"><span>◫</span>Perfil / Configuracion</a>
                <a class="active" href="{{ url('/perfil/suscripcion') }}"><span>▧</span>Suscripcion</a>
            </nav>
            <form class="sidebar-logout" action="{{ url('/logout') }}" method="post">
                @csrf
                <button type="submit"><span>↩</span>Cerrar sesion</button>
            </form>
        </aside>

        <main class="workspace-main billing-main">
            <header class="workspace-topbar">
                <div>
                    <h1>Suscripcion</h1>
                    <p>Administra tu plan, pagos y acceso premium desde tu perfil.</p>
                </div>
            </header>

            @if (session('status'))
                <section class="billing-alert">{{ session('status') }}</section>
            @endif

            <section class="billing-status-card">
                <div>
                    <span class="billing-kicker">Estado actual</span>
                    <h2>{{ $currentSubscription ? ucfirst(str_replace('_', ' ', $currentSubscription->plan)) : 'Gratis' }}</h2>
                    <p>{{ $currentSubscription ? 'Funciones premium activas para tu cuenta.' : 'Estas usando el plan gratuito con RFC propio.' }}</p>
                </div>
                <div class="billing-status-meta">
                    <span>{{ $status }}</span>
                    <strong>Renovacion: {{ $renewal }}</strong>
                </div>
            </section>

            <section class="workspace-panel">
                <div class="panel-header">
                    <div>
                        <h2>Comparacion de planes</h2>
                        <p>El plan actual queda bloqueado. Puedes cambiar o actualizar desde Mercado Pago.</p>
                    </div>
                </div>

                <div class="billing-plan-grid">
                    @foreach ($planes as $plan)
                        @php
                            $isCurrent = $currentPlan === $plan['clave'] || ($plan['clave'] === 'gratis' && ! $currentPlan);
                            $isPaid = $plan['clave'] !== 'gratis';
                        @endphp
                        <article @class(['billing-plan-card', 'is-current' => $isCurrent, 'is-popular' => $plan['popular']])>
                            @if ($plan['popular'])
                                <span class="pricing-badge">Mas popular</span>
                            @endif
                            <h3>{{ $plan['nombre'] }}</h3>
                            <div class="pricing-price compact">
                                <span>${{ number_format($plan['precio']) }}</span>
                                <small>{{ $plan['periodo'] }}</small>
                            </div>
                            <ul class="pricing-features">
                                @foreach (array_slice($plan['beneficios'], 0, 3) as $beneficio)
                                    <li>{{ $beneficio }}</li>
                                @endforeach
                            </ul>

                            @if ($isCurrent)
                                <button class="pricing-cta is-disabled" type="button" disabled>Plan actual</button>
                            @elseif ($isPaid)
                                <form method="post" action="{{ url('/subscriptions/change') }}">
                                    @csrf
                                    <input type="hidden" name="plan" value="{{ $plan['clave'] }}">
                                    <button class="pricing-cta" type="submit">{{ $currentPlan ? 'Cambiar plan' : 'Actualizar' }}</button>
                                </form>
                            @else
                                <button class="pricing-cta is-disabled" type="button" disabled>Plan base</button>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="workspace-panel">
                <div class="panel-header">
                    <div>
                        <h2>Acciones</h2>
                        <p>Controla tu acceso sin perder historial fiscal.</p>
                    </div>
                </div>
                <div class="billing-actions">
                    @if ($currentSubscription)
                        <form method="post" action="{{ url('/subscriptions/cancel') }}">
                            @csrf
                            <button class="btn btn-outline-primary danger" type="submit">Cancelar suscripcion</button>
                        </form>
                    @endif
                </div>
            </section>

            <section class="workspace-panel">
                <div class="panel-header">
                    <div>
                        <h2>Historial de pagos</h2>
                        <p>Pagos registrados y sincronizados por backend.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="fiscal-table">
                        <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Plan</th>
                            <th>Estado</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>{{ ucfirst($payment->tipo_pago) }}</td>
                                <td>{{ $payment->plan ?: 'Donacion' }}</td>
                                <td>{{ ucfirst($payment->estado) }}</td>
                                <td>${{ number_format((float) $payment->monto, 2) }}</td>
                                <td>{{ $payment->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">Aun no hay pagos registrados.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
@endsection
