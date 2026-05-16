@extends('layouts.app', [
    'title' => 'Inicio fiscal | ContaPro',
    'hideNav' => true,
])

@section('content')
    <div class="app-workspace">
        <aside class="app-sidebar" aria-label="Menú fiscal">
            <a class="workspace-brand" href="{{ url('/dashboard') }}">
                <span>CP</span>
                <strong>ContaPro</strong>
            </a>
            <nav>
                <a class="active" href="{{ url('/dashboard') }}"><span>▦</span>Inicio</a>
                <a href="{{ url('/cfdi') }}"><span>▤</span>CFDI</a>
                <a href="{{ url('/descargas/nueva') }}"><span>⇩</span>Descarga masiva</a>
                <a href="{{ url('/cfdi') }}"><span>▧</span>Reportes</a>
                <a href="{{ url('/dashboard') }}"><span>▣</span>Declaraciones</a>
                <a href="{{ url('/perfil') }}"><span>◫</span>Perfil / Configuración</a>
            </nav>
            <form class="sidebar-logout" action="{{ url('/logout') }}" method="post">
                @csrf
                <button type="submit"><span>↩</span>Cerrar sesión</button>
            </form>
        </aside>

        <main class="workspace-main">
            <header class="workspace-topbar">
                <div>
                    <h1>Inicio</h1>
                    <p>Estado del sistema y conexión SAT</p>
                </div>
                <div class="workspace-actions">
                    <div class="workspace-user">
                        <span>{{ mb_substr(auth()->user()?->name ?? 'C', 0, 1) }}</span>
                        <div>
                            <strong>{{ auth()->user()?->name ?? 'Contador' }}</strong>
                            <small>{{ auth()->user()?->email ?? 'Cuenta verificada' }}</small>
                        </div>
                    </div>
                </div>
            </header>

            @if (session('status'))
                <section class="orientation-box">
                    <div>
                        <strong>{{ session('status') }}</strong>
                        <p>La operación CFDI se habilita después de validar tu conexión con el SAT.</p>
                    </div>
                </section>
            @endif

            <section class="orientation-box">
                <div>
                    <strong>Estado de cuenta</strong>
                    <p>RFC activo: <b>{{ $profile?->rfc ?? 'Pendiente' }}</b> · Periodo: <b>{{ ucfirst($periodLabel) }}</b> · Tipo: <b>{{ $profile?->user_type ?? 'Pendiente' }}</b></p>
                </div>
            </section>

            <section id="efirma-panel" class="efirma-panel" aria-labelledby="efirmaTitle">
                <div class="efirma-title">
                    <span>🛡</span>
                    <div>
                        <h2 id="efirmaTitle">Autenticación con e.firma</h2>
                        <p>Carga tus archivos .cer y .key para autenticarte con el SAT.</p>
                    </div>
                </div>

                <div class="security-note">
                    <span>🛡</span>
                    <div>
                        <strong>Tu seguridad es nuestra prioridad</strong>
                        <p>Tus archivos se procesan de forma segura. La validación se realiza durante la solicitud y no se muestran datos sensibles en pantalla.</p>
                    </div>
                </div>

                <div class="sat-connection-status {{ session('sat_authenticated') ? 'is-connected' : 'is-disconnected' }}" data-sat-status>
                    <strong data-sat-status-title>{{ session('sat_authenticated') ? 'Conexión con el SAT validada correctamente' : 'Conexión con el SAT no establecida' }}</strong>
                    <p data-sat-status-copy>{{ session('sat_authenticated') ? 'La e.firma fue validada contra el SAT. Ya puedes trabajar la información CFDI desde la sección CFDI.' : 'Carga tu certificado, llave privada y contraseña para validar la e.firma antes de intercambiar información con el SAT.' }}</p>
                </div>

                <form class="efirma-form-grid" method="post" action="{{ route('sat.auth') }}" enctype="multipart/form-data" data-sat-auth-form>
                    @csrf
                    <div class="efirma-field full">
                        <label for="dashboard_cer">Certificado (.cer)</label>
                        <label class="file-drop" for="dashboard_cer">
                            <span>▤</span>
                            <strong>Selecciona tu archivo .cer</strong>
                            <small>Haz clic para buscar el archivo</small>
                        </label>
                        <input id="dashboard_cer" name="cer" type="file" accept=".cer" required>
                    </div>

                    <div class="efirma-field full">
                        <label for="dashboard_key">Llave privada (.key)</label>
                        <label class="file-drop" for="dashboard_key">
                            <span>⚿</span>
                            <strong>Selecciona tu archivo .key</strong>
                            <small>Haz clic para buscar el archivo</small>
                        </label>
                        <input id="dashboard_key" name="key" type="file" accept=".key" required>
                    </div>

                    <div class="efirma-field full">
                        <label for="dashboard_efirma_password">Contraseña de la llave privada</label>
                        <input id="dashboard_efirma_password" name="password" type="password" placeholder="Ingresa tu contraseña" autocomplete="off" required>
                    </div>

                    <button class="btn btn-outline-primary" type="button" data-efirma-clear>Limpiar</button>
                    <button class="btn btn-primary" type="button" data-efirma-connect @disabled(session('sat_authenticated'))>{{ session('sat_authenticated') ? 'Conexión establecida' : 'Conectar e.firma' }}</button>
                </form>
            </section>

        </main>
    </div>

    <script>
        (() => {
            const status = document.querySelector('[data-sat-status]');
            const form = document.querySelector('[data-sat-auth-form]');
            const statusTitle = document.querySelector('[data-sat-status-title]');
            const statusCopy = document.querySelector('[data-sat-status-copy]');
            const connect = document.querySelector('[data-efirma-connect]');
            const clear = document.querySelector('[data-efirma-clear]');
            const fields = [
                document.getElementById('dashboard_cer'),
                document.getElementById('dashboard_key'),
                document.getElementById('dashboard_efirma_password'),
            ];

            const setDisconnected = () => {
                status?.classList.remove('is-connected');
                status?.classList.add('is-disconnected');
                if (statusTitle) statusTitle.textContent = 'Conexión con el SAT no establecida';
                if (statusCopy) statusCopy.textContent = 'Carga tu certificado, llave privada y contraseña para validar la e.firma antes de intercambiar información con el SAT.';
                if (connect) {
                    connect.textContent = 'Conectar e.firma';
                    connect.disabled = false;
                }
                fields.forEach((field) => {
                    if (field) field.disabled = false;
                });
            };

            connect?.addEventListener('click', async () => {
                const ready = fields.every((field) => field && field.value);

                if (!ready) {
                    setDisconnected();
                    status?.classList.add('status-attention');
                    window.setTimeout(() => status?.classList.remove('status-attention'), 500);
                    return;
                }

                connect.disabled = true;
                connect.textContent = 'Validando con SAT...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                    });
                    const payload = await response.json();

                    if (!response.ok || !payload.ok) {
                        throw new Error(payload.message || 'No fue posible validar la e.firma. Verifica tus archivos y contraseña.');
                    }

                    status?.classList.remove('is-disconnected');
                    status?.classList.add('is-connected');
                    if (statusTitle) statusTitle.textContent = 'Conexión con el SAT validada correctamente';
                    if (statusCopy) statusCopy.textContent = 'La e.firma fue validada contra el SAT. Ya puedes trabajar la información CFDI desde la sección CFDI.';
                    connect.textContent = 'Conexión establecida';
                    fields.forEach((field) => {
                        if (field) field.disabled = true;
                    });
                } catch (error) {
                    setDisconnected();
                    if (statusCopy) statusCopy.textContent = 'No fue posible validar la e.firma. Verifica tus archivos y contraseña.';
                    status?.classList.add('status-attention');
                    window.setTimeout(() => status?.classList.remove('status-attention'), 500);
                }
            });

            clear?.addEventListener('click', () => {
                fields.forEach((field) => {
                    if (field) field.value = '';
                });
                setDisconnected();
            });
        })();
    </script>
@endsection
