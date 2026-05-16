@extends('layouts.app', ['title' => __('app.descarga.title')])

@section('content')
    <section class="app-shell py-4">
        <div class="container">
            <div class="mb-4">
                <h1 class="h3 fw-bold mb-1">{{ __('app.descarga.heading') }}</h1>
                <p class="text-secondary mb-0">{{ __('app.descarga.subtitle') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            @if (session('show_donation_prompt'))
                <div class="donation-banner">
                    <div>
                        <strong>Apoya el desarrollo de herramientas gratuitas para la comunidad.</strong>
                        <p>Tu aportacion ayuda a mantener disponible la descarga gratuita de CFDI para mas usuarios.</p>
                    </div>
                    <a class="btn btn-primary" href="{{ url('/donaciones/mercadopago') }}">Realizar donacion</a>
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-error">{{ $errors->first() }}</div>
            @endif

            <form class="panel" method="post" action="{{ url('/descargas') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="rfc">{{ __('app.rfc.rfc') }}</label>
                        <input id="rfc" class="form-control" name="rfc" maxlength="13" value="{{ old('rfc', $canUseMultipleRfcs ? '' : $profile?->rfc) }}" @readonly(! $canUseMultipleRfcs) required aria-describedby="rfcHelp rfcFeedback" data-rfc-mask>
                        <div id="rfcHelp" class="form-text">
                            {{ $canUseMultipleRfcs ? 'Puedes descargar CFDI de tus clientes registrados.' : 'Modo gratuito: solo el RFC registrado en tu perfil.' }}
                        </div>
                        <div id="rfcFeedback" class="form-text rfc-feedback" data-rfc-feedback="rfc">Formato: 12 o 13 caracteres con homoclave.</div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="fecha_inicio">{{ __('app.descarga.start_date') }}</label>
                        <input id="fecha_inicio" class="form-control" name="fecha_inicio" type="date" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="fecha_fin">{{ __('app.descarga.end_date') }}</label>
                        <input id="fecha_fin" class="form-control" name="fecha_fin" type="date" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="tipo">{{ __('app.descarga.type') }}</label>
                        <select id="tipo" class="form-select" name="tipo" required>
                            <option value="emitidos">{{ __('app.descarga.issued') }}</option>
                            <option value="recibidos">{{ __('app.descarga.received') }}</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center mt-4">
                    <button class="btn btn-primary" type="submit">{{ __('app.descarga.submit') }}</button>
                    <a class="btn btn-outline-primary" href="{{ url('/dashboard#efirma-panel') }}">Revalidar e.firma</a>
                </div>
            </form>

            <section class="workspace-panel mx-0 mt-4">
                <div class="panel-header">
                    <div>
                        <h2>Límites y requisitos del SAT</h2>
                        <p>Usa tu e.firma para solicitar comprobantes fiscales directamente al SAT.</p>
                    </div>
                </div>
                <div class="sat-help-grid">
                    <div>
                        <strong>Modo gratuito</strong>
                        <p>Disponible para todos los perfiles con el RFC registrado. Máximo 1 mes por consulta, dentro del año en curso.</p>
                    </div>
                    <div>
                        <strong>Suscripción</strong>
                        <p>Contadores y despachos pueden habilitar clientes, múltiples RFC, rangos amplios y reportes avanzados.</p>
                    </div>
                    <div>
                        <strong>Seguridad e.firma</strong>
                        <p>Los archivos se usan para autenticar la solicitud SAT y no deben almacenarse en el servidor.</p>
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection
