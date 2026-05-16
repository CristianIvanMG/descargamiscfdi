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
                    <p>Resumen fiscal del mes actual</p>
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

            <section class="dashboard-help-section" aria-labelledby="helpTitle">
                <div class="dashboard-help-heading">
                    <h2 id="helpTitle">Información y Ayuda</h2>
                    <p>Todo lo que necesitas saber sobre la descarga masiva de CFDI.</p>
                </div>

                <div class="help-grid">
                    <article class="help-card">
                        <h3>▥ ¿Cómo usar la herramienta?</h3>
                        @foreach ([
                            ['Conecta tu e.firma', 'Carga tu certificado (.cer), llave privada (.key) e ingresa tu contraseña para autenticarte con el SAT.'],
                            ['Crea una solicitud', 'Selecciona el rango de fechas, tipo de comprobantes emitidos o recibidos y formato de descarga.'],
                            ['Espera el procesamiento', 'El SAT procesa tu solicitud. El tiempo varía según el volumen de comprobantes.'],
                            ['Verifica el estado', 'Consulta periódicamente si la solicitud ya está lista para descarga.'],
                            ['Descarga tus archivos', 'Cuando la solicitud esté lista, descarga los paquetes ZIP con tus XML.'],
                        ] as $index => $step)
                            <div class="help-step">
                                <span>{{ $index + 1 }}</span>
                                <div>
                                    <strong>{{ $step[0] }}</strong>
                                    <p>{{ $step[1] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </article>

                    <article class="help-card">
                        <h3>⚠ Límites y requisitos del SAT</h3>
                        @foreach ([
                            ['Rango de fechas', 'Máximo 1 mes por solicitud.'],
                            ['Antigüedad', 'Hasta 6 años de historial disponible.'],
                            ['Tiempo de procesamiento', 'De minutos a horas según volumen.'],
                            ['Token de sesión', 'Válido por 5 minutos; se renueva automáticamente.'],
                            ['Solicitudes simultáneas', 'Máximo 2 solicitudes en proceso a la vez.'],
                        ] as $item)
                            <div class="help-item">
                                <span>!</span>
                                <div>
                                    <strong>{{ $item[0] }}</strong>
                                    <p>{{ $item[1] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </article>
                </div>
            </section>

            <section class="legal-panel" aria-labelledby="legalTitle">
                <h2 id="legalTitle">⚖ Avisos legales</h2>
                @foreach ([
                    ['Servicio oficial del SAT:', 'Esta herramienta utiliza los servicios web oficiales del Servicio de Administración Tributaria (SAT) de México para la descarga masiva de comprobantes fiscales digitales.'],
                    ['Seguridad de tu e.firma:', 'Tu e.firma (FIEL) se procesa de forma segura. Los archivos .cer y .key se utilizan únicamente para firmar las solicitudes al SAT y no se almacenan en nuestros servidores. La contraseña nunca sale de tu navegador.'],
                    ['Responsabilidad del usuario:', 'El usuario es responsable del uso correcto de su e.firma y de la confidencialidad de sus credenciales. No compartas tu llave privada ni tu contraseña con terceros.'],
                    ['Datos fiscales:', 'Los comprobantes descargados son documentos oficiales emitidos o recibidos por el contribuyente. La información contenida en ellos es confidencial y debe manejarse conforme a las leyes aplicables.'],
                    ['Disponibilidad del servicio:', 'La disponibilidad del servicio de descarga masiva depende del SAT. En ocasiones puede experimentar lentitud o interrupciones temporales debido a mantenimiento o alta demanda.'],
                ] as $notice)
                    <p><span>ⓘ</span><b>{{ $notice[0] }}</b> {{ $notice[1] }}</p>
                @endforeach
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
