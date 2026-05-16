@extends('layouts.app', ['title' => __('app.descarga.title')])

@section('content')
    <section class="app-shell py-4" x-data="contadorMxEfirma()">
        <div class="container">
            <div class="mb-4">
                <h1 class="h3 fw-bold mb-1">{{ __('app.descarga.heading') }}</h1>
                <p class="text-secondary mb-0">{{ __('app.descarga.subtitle') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
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
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="cer">{{ __('app.descarga.cer') }}</label>
                        <input id="cer" class="form-control" type="file" accept=".cer" x-on:change="setCer" aria-label="{{ __('app.descarga.cer') }}" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="key">{{ __('app.descarga.key') }}</label>
                        <input id="key" class="form-control" type="file" accept=".key" x-on:change="setKey" aria-label="{{ __('app.descarga.key') }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="efirma_password">{{ __('app.descarga.password') }}</label>
                        <input id="efirma_password" class="form-control" type="password" x-model="password" aria-label="{{ __('app.descarga.password') }}" autocomplete="off" required>
                    </div>
                    <input type="hidden" name="signed_token" x-model="signedToken">
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center mt-4">
                    <button class="btn btn-outline-primary" type="button" x-on:click="signChallenge()">{{ __('app.descarga.sign') }}</button>
                    <button class="btn btn-primary" type="submit" :disabled="signedToken.length === 0">{{ __('app.descarga.submit') }}</button>
                    <span class="text-danger" x-text="error"></span>
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
                        <p>Disponible para todos los perfiles con el RFC registrado. Máximo 6 meses dentro del año en curso.</p>
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

@push('scripts')
    <script src="{{ asset('js/efirma.js') }}"></script>
@endpush
