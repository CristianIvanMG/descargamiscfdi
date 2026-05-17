@extends('layouts.app', [
    'title' => 'CFDI | ContaPro',
    'hideNav' => true,
])

@section('content')
    @php
        $canHistory = \App\Support\MembershipAccess::canAccessFullHistory(auth()->user());
        $currentYear = now()->year;
        $yearStart = now()->startOfYear()->toDateString();
        $today = now()->toDateString();
    @endphp

    <div class="app-workspace">
        <aside class="app-sidebar" aria-label="Menú fiscal">
            <a class="workspace-brand" href="{{ url('/dashboard') }}">
                <span>CP</span>
                <strong>ContaPro</strong>
            </a>
            <nav>
                <a href="{{ url('/dashboard') }}"><span>▦</span>Inicio</a>
                <a class="active" href="{{ url('/cfdi') }}"><span>▤</span>CFDI</a>
                <a href="{{ url('/descargas/nueva') }}"><span>⇩</span>Descarga masiva</a>
                @if ($canHistory)
                    <a href="{{ url('/historial') }}"><span>▧</span>Historial</a>
                @endif
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
                    <h1>CFDI</h1>
                    <p>Consulta y descarga CFDI emitidos y recibidos del SAT.</p>
                </div>
                <div class="workspace-actions">
                    <a class="btn btn-primary" href="{{ session('sat_authenticated') ? url('/descargas/nueva') : url('/dashboard#efirma-panel') }}">Descargar CFDI</a>
                    <a class="btn btn-outline-primary" href="{{ url('/cfdi/exportar?'.http_build_query($filters)) }}">Exportar a Excel</a>
                    <a class="btn btn-outline-primary" href="{{ url('/suscripcion/planes') }}">Ver planes</a>
                </div>
            </header>

            @unless (session('sat_authenticated'))
                <section class="sat-connection-status is-disconnected cfdi-blocker">
                    <strong>Conexión con el SAT no establecida</strong>
                    <p>Primero valida tu e.firma en Inicio. Puedes revisar esta pantalla, pero no iniciar solicitudes hasta conectar con SAT.</p>
                    <a class="btn btn-primary" href="{{ url('/dashboard#efirma-panel') }}">Conectar e.firma</a>
                </section>
            @endunless

            @if (session('show_donation_prompt'))
                <section class="donation-banner">
                    <div>
                        <strong>Apoya el desarrollo de herramientas gratuitas para la comunidad.</strong>
                        <p>Estás apoyando herramientas gratuitas para contadores en México.</p>
                    </div>
                    <a class="btn btn-primary" href="{{ url('/donaciones/mercadopago') }}">Realizar donación</a>
                </section>
            @endif

            @if ($showUpgradePrompt)
                <section class="sat-connection-status status-attention">
                    <strong>Gestiona múltiples RFC con un plan premium</strong>
                    <p>Ahorra hasta 70% del tiempo vs SAT. Desbloquea clientes, rangos avanzados y mayor volumen.</p>
                    <a class="btn btn-primary" href="{{ url('/suscripcion/planes') }}">Ver planes</a>
                </section>
            @endif

            <section class="orientation-box">
                <div>
                    <strong>CFDI - Descarga y gestión</strong>
                    <p>
                        @if ($canUseMultipleRfcs)
                            Tu plan permite trabajar múltiples RFC y clientes.
                        @else
                            Modo gratuito: RFC bloqueado a tu perfil. Solicitudes al SAT solo del año {{ $currentYear }} y por mes.
                        @endif
                    </p>
                </div>
            </section>

            <section class="workspace-panel cfdi-guide-panel step-section step-section-primary">
                <div class="panel-header">
                    <div>
                        <span class="step-badge">⚙️ Paso 1</span>
                        <h2>Paso 1: Configura tu descarga</h2>
                        <p>Trabaja con menos pasos y con controles fiscales desde el inicio.</p>
                    </div>
                </div>
                <div class="cfdi-step-grid compact">
                    <article class="cfdi-step active">
                        <span>1</span>
                        <strong>Define el periodo</strong>
                        <p>En modo gratuito solo se permite consultar por mes dentro del año actual.</p>
                    </article>
                    <article class="cfdi-step {{ session('sat_authenticated') ? 'done' : 'error' }}">
                        <span>2</span>
                        <strong>Valida SAT</strong>
                        <p>{{ session('sat_authenticated') ? 'Tu e.firma ya está conectada.' : 'Conecta tu e.firma desde Inicio antes de solicitar.' }}</p>
                    </article>
                    <article class="cfdi-step pending">
                        <span>3</span>
                        <strong>Solicita y descarga</strong>
                        <p>El sistema guarda la solicitud y muestra el estado cuando SAT libere paquetes.</p>
                    </article>
                </div>
            </section>

            <section class="cfdi-flow-shell step-section">
                <div class="step-section-heading">
                    <span class="step-badge">📄 Paso 2</span>
                    <div>
                        <h2>Paso 2: Selecciona el tipo de CFDI</h2>
                        <p>Elige emitidos o recibidos y captura los parámetros de solicitud en una sola vista.</p>
                    </div>
                </div>

                <div class="cfdi-main-tabs" role="tablist" aria-label="Tipo de CFDI">
                    <button class="active" type="button" data-cfdi-tab="emitidos">CFDI Emitidos</button>
                    <button type="button" data-cfdi-tab="recibidos">CFDI Recibidos</button>
                </div>

                @foreach ([
                    'emitidos' => ['title' => 'CFDI Emitidos', 'type' => 'emitidos'],
                    'recibidos' => ['title' => 'CFDI Recibidos', 'type' => 'recibidos'],
                ] as $tabKey => $tab)
                    <div class="cfdi-tab-panel {{ $tabKey === 'emitidos' ? 'active' : '' }}" data-cfdi-panel="{{ $tabKey }}">
                        <div class="cfdi-step-grid cfdi-process-detail">
                            <article class="cfdi-step active">
                                <span>1</span>
                                <strong>Configura</strong>
                                <p>Selecciona RFC, fechas y tipo {{ $tab['type'] }}. En modo gratuito el RFC queda bloqueado y el rango máximo es 1 mes.</p>
                            </article>
                            <article class="cfdi-step {{ session('sat_authenticated') ? 'done' : 'error' }}">
                                <span>2</span>
                                <strong>Autenticación SAT</strong>
                                <p>{{ session('sat_authenticated') ? 'Sesión SAT validada correctamente.' : 'Primero valida tu e.firma en Inicio.' }}</p>
                            </article>
                            <article class="cfdi-step pending">
                                <span>3</span>
                                <strong>Solicita</strong>
                                <p>Se crea la solicitud SAT y se guarda el RequestId para seguimiento.</p>
                            </article>
                            <article class="cfdi-step pending">
                                <span>4</span>
                                <strong>Espera</strong>
                                <p>Verifica estado: pendiente, en proceso, terminado o error.</p>
                            </article>
                            <article class="cfdi-step pending">
                                <span>5</span>
                                <strong>Descarga</strong>
                                <p>Descarga paquetes ZIP/XML cuando el SAT los libere.</p>
                            </article>
                        </div>

                        <form class="cfdi-request-panel" method="post" action="{{ url('/descargas') }}" data-free-mode="{{ $canUseMultipleRfcs ? '0' : '1' }}">
                            @csrf
                            <input type="hidden" name="tipo" value="{{ $tab['type'] }}">
                            <div>
                                <label>RFC</label>
                                <input name="rfc" value="{{ $canUseMultipleRfcs ? $filters['rfc'] : $profile?->rfc }}" placeholder="RFC del perfil o cliente" @readonly(! $canUseMultipleRfcs) data-rfc-mask required>
                            </div>
                            <div>
                                <label>Fecha inicio</label>
                                <input name="fecha_inicio" type="date" value="{{ $filters['fecha_inicio'] }}" @if(! $canUseMultipleRfcs) min="{{ $yearStart }}" max="{{ $today }}" @endif required data-start-date>
                            </div>
                            <div>
                                <label>Fecha fin</label>
                                <input name="fecha_fin" type="date" value="{{ $filters['fecha_fin'] }}" @if(! $canUseMultipleRfcs) min="{{ $yearStart }}" max="{{ $today }}" @endif required data-end-date>
                            </div>
                            <div>
                                <label>Tipo CFDI</label>
                                <input value="{{ ucfirst($tab['type']) }}" readonly>
                            </div>
                            <button class="btn btn-primary" type="submit" @disabled(! session('sat_authenticated'))>Solicitar descarga</button>
                        </form>

                        <div class="cfdi-status-row">
                            <div class="cfdi-status active">Configura</div>
                            <div class="cfdi-status pending">Solicitud enviada al SAT</div>
                            <div class="cfdi-status processing">Procesando información</div>
                            <div class="cfdi-status done">Listo para descarga</div>
                            <div class="cfdi-status error">Error</div>
                        </div>
                    </div>
                @endforeach
            </section>

            <section class="fiscal-kpi-grid" aria-label="Indicadores CFDI">
                @foreach ([
                    ['label' => 'CFDI emitidos', 'value' => number_format($metrics['emitidos_count']), 'note' => 'Total de comprobantes del periodo'],
                    ['label' => 'Monto emitido acumulado', 'value' => '$'.number_format($metrics['emitidos_total'], 2), 'note' => 'Ingresos detectados por CFDI'],
                    ['label' => 'IVA trasladado', 'value' => '$'.number_format($metrics['iva_trasladado'], 2), 'note' => 'Base para declaración'],
                    ['label' => 'CFDI recibidos', 'value' => number_format($metrics['recibidos_count']), 'note' => 'Comprobantes recibidos del periodo'],
                    ['label' => 'Gastos detectados', 'value' => '$'.number_format($metrics['recibidos_total'], 2), 'note' => 'Egresos deducibles por revisar'],
                    ['label' => 'IVA acreditable', 'value' => '$'.number_format($metrics['iva_acreditable'], 2), 'note' => 'Base para revisión fiscal'],
                ] as $metric)
                    <article class="fiscal-kpi">
                        <span>{{ $metric['label'] }}</span>
                        <strong>{{ $metric['value'] }}</strong>
                        <small>{{ $metric['note'] }}</small>
                    </article>
                @endforeach
            </section>

            <section class="workspace-panel cfdi-viewer-panel">
                <div class="panel-header">
                    <div>
                        <span class="step-badge">📊 Visualizador</span>
                        <h2>Visualizador de CFDI</h2>
                        <p>Consulta, filtra y descarga tus comprobantes CFDI de forma sencilla. Usa los filtros para encontrar información específica y exporta los resultados en Excel cuando lo necesites.</p>
                    </div>
                    <a class="btn btn-primary" href="{{ url('/cfdi/exportar?'.http_build_query($filters)) }}">Exportar a Excel</a>
                </div>

                <form class="cfdi-filter-panel cfdi-viewer-filters" method="get" action="{{ url('/cfdi') }}" data-free-mode="{{ $canUseMultipleRfcs ? '0' : '1' }}">
                    <div>
                        <label for="tipo">Tipo CFDI</label>
                        <select id="tipo" name="tipo">
                            <option value="todos" @selected($filters['tipo'] === 'todos')>Todos</option>
                            <option value="emitidos" @selected($filters['tipo'] === 'emitidos')>Emitidos</option>
                            <option value="recibidos" @selected($filters['tipo'] === 'recibidos')>Recibidos</option>
                        </select>
                    </div>
                    <div>
                        <label for="fecha_inicio">Fecha inicial</label>
                        <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ $filters['fecha_inicio'] }}" @if(! $canUseMultipleRfcs) min="{{ $yearStart }}" max="{{ $today }}" @endif data-start-date>
                    </div>
                    <div>
                        <label for="fecha_fin">Fecha final</label>
                        <input id="fecha_fin" name="fecha_fin" type="date" value="{{ $filters['fecha_fin'] }}" @if(! $canUseMultipleRfcs) min="{{ $yearStart }}" max="{{ $today }}" @endif data-end-date>
                    </div>
                    <div>
                        <label for="rfc">RFC</label>
                        <input id="rfc" name="rfc" value="{{ $filters['rfc'] }}" placeholder="Filtrar por RFC" @readonly(! $canUseMultipleRfcs) data-rfc-mask>
                    </div>
                    <div>
                        <label for="estatus">Estatus</label>
                        <select id="estatus" name="estatus">
                            <option value="">Todos</option>
                            <option value="vigente">Vigente</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit">Aplicar filtros</button>
                </form>

                <div class="cfdi-viewer-actions">
                    <a class="btn btn-outline-primary" href="{{ url('/descargas/nueva') }}">Descargar XML</a>
                    <button class="btn btn-outline-primary" type="button" disabled>Descargar PDF</button>
                    <a class="btn btn-primary" href="{{ url('/cfdi/exportar?'.http_build_query($filters)) }}">Exportar a Excel</a>
                </div>

                <div class="table-responsive">
                    <table class="fiscal-table">
                        <thead>
                        <tr>
                            <th>UUID</th>
                            <th>Fecha</th>
                            <th>Emisor / Receptor</th>
                            <th>Estatus</th>
                            <th class="text-end">Monto</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">{{ __('app.cfdi.empty') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        (() => {
            const tabs = document.querySelectorAll('[data-cfdi-tab]');
            const panels = document.querySelectorAll('[data-cfdi-panel]');
            const today = @json($today);
            const yearStart = @json($yearStart);

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const target = tab.dataset.cfdiTab;
                    tabs.forEach((item) => item.classList.toggle('active', item === tab));
                    panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.cfdiPanel === target));
                });
            });

            const addOneMonth = (value) => {
                const date = new Date(`${value}T00:00:00`);
                date.setMonth(date.getMonth() + 1);
                return date.toISOString().slice(0, 10);
            };

            document.querySelectorAll('[data-free-mode="1"]').forEach((form) => {
                const start = form.querySelector('[data-start-date]');
                const end = form.querySelector('[data-end-date]');
                if (!start || !end) return;

                const clamp = () => {
                    if (start.value && start.value < yearStart) start.value = yearStart;
                    if (start.value && start.value > today) start.value = today;
                    if (!start.value) return;

                    const maxEnd = [addOneMonth(start.value), today].sort()[0];
                    end.min = start.value;
                    end.max = maxEnd;

                    if (!end.value || end.value < start.value || end.value > maxEnd) {
                        end.value = maxEnd;
                    }
                };

                start.addEventListener('change', clamp);
                end.addEventListener('change', clamp);
                clamp();
            });
        })();
    </script>
@endsection
