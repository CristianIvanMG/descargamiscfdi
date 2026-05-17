@extends('layouts.app', [
    'title' => 'Perfil de negocio | ContaPro',
    'hideNav' => true,
])

@section('content')
    @php
        $isComplete = $profile->isComplete();
        $canHistory = \App\Support\MembershipAccess::canAccessFullHistory(auth()->user());
    @endphp

    <div class="app-workspace">
        <aside class="app-sidebar" aria-label="Menu fiscal">
            <a class="workspace-brand" href="{{ $isComplete ? url('/dashboard') : url('/perfil') }}">
                <span>CP</span>
                <strong>ContaPro</strong>
            </a>

            @if ($isComplete)
                <nav>
                    <a href="{{ url('/dashboard') }}"><span>▦</span>Inicio</a>
                    <a href="{{ url('/cfdi') }}"><span>▤</span>CFDI</a>
                    <a href="{{ url('/descargas/nueva') }}"><span>⇩</span>Descarga masiva</a>
                    @if ($canHistory)
                        <a href="{{ url('/historial') }}"><span>▧</span>Historial</a>
                    @endif
                    <a href="{{ url('/suscripcion/mi') }}"><span>◩</span>Suscripcion</a>
                    <a class="active" href="{{ url('/perfil') }}"><span>◫</span>Perfil / Configuracion</a>
                </nav>
            @else
                <div class="sidebar-locked">
                    <strong>Configuracion requerida</strong>
                    <p>Completa tu perfil para habilitar el inicio y las herramientas CFDI.</p>
                </div>
            @endif

            <form class="sidebar-logout" action="{{ url('/logout') }}" method="post">
                @csrf
                <button type="submit"><span>↩</span>Cerrar sesion</button>
            </form>
        </aside>

        <main class="workspace-main">
            <header class="workspace-topbar">
                <div>
                    <h1>Perfil de negocio</h1>
                    <p>{{ $isComplete ? 'Datos fiscales fijos del entorno de trabajo.' : 'Completa tu entorno de trabajo para empezar con CFDI.' }}</p>
                </div>
            </header>

            <section class="workspace-panel profile-panel">
                <div class="panel-header">
                    <div>
                        <h2>{{ $isComplete ? 'Configuracion del perfil' : 'Completa tu perfil para empezar a trabajar con CFDI.' }}</h2>
                        <p>{{ $isComplete ? 'Nombre, RFC y tipo de usuario quedan fijos para proteger permisos, historial y suscripciones.' : 'Todos los campos son obligatorios. Esta configuracion protege el flujo fiscal y evita datos incompletos.' }}</p>
                    </div>
                </div>

                @if (session('status'))
                    <div class="auth-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-error">Ocurrio un problema al cargar tu informacion. Intenta nuevamente.</div>
                @endif

                <form class="profile-form" action="{{ url('/perfil') }}" method="post">
                    @csrf
                    <div>
                        <label for="business_name" data-profile-name-label>Nombre del contador / empresa</label>
                        <input id="business_name" name="business_name" type="text" value="{{ old('business_name', $profile->business_name) }}" placeholder="NOMBRE PERSONAL O DE LA ORGANIZACION" required data-name-mask data-profile-name-input data-registered-name="{{ auth()->user()?->name }}" @readonly($isComplete)>
                        <small class="field-help" data-profile-name-help>{{ $isComplete ? 'Dato fijo del perfil fiscal. Para cambiarlo contacta soporte.' : 'Selecciona el tipo de usuario para definir si representa a una persona o una organizacion.' }}</small>
                    </div>
                    <div>
                        <label for="rfc">RFC</label>
                        <input id="rfc" name="rfc" type="text" value="{{ old('rfc', $profile->rfc) }}" maxlength="13" placeholder="RFC CON HOMOCLAVE" required data-rfc-mask aria-describedby="rfcFeedback" @readonly($isComplete)>
                        <small id="rfcFeedback" class="rfc-feedback" data-rfc-feedback="rfc">Formato: 12 o 13 caracteres con homoclave.</small>
                    </div>
                    <div>
                        <label for="type">Tipo de usuario</label>
                        @if ($isComplete)
                            <input type="text" value="{{ $profile->user_type }}" readonly class="readonly-field">
                        @endif
                        <select id="type" name="user_type" required @disabled($isComplete) @class(['visually-hidden' => $isComplete])>
                            <option value="">Selecciona una opcion</option>
                            <option value="Persona fisica" @selected(in_array(old('user_type', $profile->user_type), ['Persona fisica', 'Persona física', 'Persona fÃ­sica'], true))>Persona fisica</option>
                            <option value="Contador independiente" @selected(old('user_type', $profile->user_type) === 'Contador independiente')>Contador independiente</option>
                            <option value="Despacho contable" @selected(old('user_type', $profile->user_type) === 'Despacho contable')>Despacho contable</option>
                        </select>
                        <small class="field-help">{{ $isComplete ? 'Dato fijo usado para permisos y suscripcion.' : 'Este dato define permisos gratuitos y premium.' }}</small>
                    </div>
                    <div>
                        <label for="primary_email">Correo principal</label>
                        <div class="validated-input-wrap">
                            <input id="primary_email" name="primary_email" type="email" value="{{ old('primary_email', $profile->primary_email) }}" placeholder="correo@despacho.com" required data-email-validation="strict" aria-describedby="profileEmailFeedback">
                            <span class="valid-icon" aria-hidden="true">✓</span>
                        </div>
                        <small id="profileEmailFeedback" class="input-feedback" data-email-feedback="primary_email"></small>
                    </div>
                    <div>
                        <label for="country">Zona fiscal / Estado</label>
                        <select id="country" name="country" required data-state-combobox>
                            <option value="">Selecciona un estado</option>
                            @foreach ($estados as $estado)
                                <option @selected(old('country', $profile->country) === $estado)>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="profile-actions">
                        <button class="btn btn-primary" type="submit">{{ $isComplete ? 'Guardar' : 'Guardar y entrar al inicio' }}</button>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script>
        (() => {
            const type = document.getElementById('type');
            const nameInput = document.querySelector('[data-profile-name-input]');
            const nameLabel = document.querySelector('[data-profile-name-label]');
            const nameHelp = document.querySelector('[data-profile-name-help]');
            const profileComplete = @json($isComplete);

            const syncProfileName = () => {
                if (!type || !nameInput || !nameLabel || !nameHelp || profileComplete) return;

                if (type.value === 'Despacho contable') {
                    nameLabel.textContent = 'Nombre de la organizacion / despacho';
                    nameInput.placeholder = 'EJ. DESPACHO HERNANDEZ Y ASOCIADOS';
                    nameInput.readOnly = false;
                    nameInput.classList.remove('readonly-field');
                    nameHelp.textContent = 'Este nombre representa a la organizacion que administrara clientes y RFC.';
                    return;
                }

                if (type.value === 'Persona fisica' || type.value === 'Contador independiente') {
                    nameLabel.textContent = 'Nombre personal';
                    nameInput.placeholder = 'NOMBRE DEL TITULAR';
                    nameHelp.textContent = 'Este nombre representa a la persona titular del perfil.';
                    return;
                }

                nameLabel.textContent = 'Nombre del contador / empresa';
                nameInput.placeholder = 'NOMBRE PERSONAL O DE LA ORGANIZACION';
                nameInput.readOnly = false;
                nameInput.classList.remove('readonly-field');
                nameHelp.textContent = 'Selecciona el tipo de usuario para definir si representa a una persona o una organizacion.';
            };

            type?.addEventListener('change', syncProfileName);
            syncProfileName();
        })();
    </script>
@endsection
