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
        <aside class="app-sidebar" aria-label="Menu fiscal">
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
                <a href="{{ url('/perfil') }}"><span>◫</span>Perfil / Configuracion</a>
            </nav>
            <form class="sidebar-logout" action="{{ url('/logout') }}" method="post">
                @csrf
                <button type="submit"><span>↩</span>Cerrar sesion</button>
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
                    <strong>Conexion con el SAT no establecida</strong>
                    <p>Primero valida tu e.firma en Inicio. Puedes revisar esta pantalla, pero no iniciar solicitudes hasta conectar con SAT.</p>
                    <a class="btn btn-primary" href="{{ url('/dashboard#efirma-panel') }}">Conectar e.firma</a>
                </section>
            @endunless

            @if (session('show_donation_prompt'))
                <section class="donation-banner">
                    <div>
                        <strong>Apoya el desarrollo de herramientas gratuitas para la comunidad.</strong>
                        <p>Estas apoyando herramientas gratuitas para contadores en Mexico.</p>
                    </div>
                    <a class="btn btn-primary" href="{{ url('/donaciones/mercadopago') }}">Realizar donacion</a>
                </section>
            @endif

            @if ($showUpgradePrompt)
                <section class="sat-connection-status status-attention">
                    <strong>Gestiona multiples RFC con un plan premium</strong>
                    <p>Ahorra hasta 70% del tiempo vs SAT. Desbloquea clientes, rangos avanzados y mayor volumen.</p>
                    <a class="btn btn-primary" href="{{ url('/suscripcion/planes') }}">Ver planes</a>
                </section>
            @endif

            <section class="orientation-box">
                <div>
                    <strong>CFDI - Descarga y gestion</strong>
                    <p>
                        @if ($canUseMultipleRfcs)
                            Tu plan permite trabajar multiples RFC y clientes.
                        @else
                            Modo gratuito: RFC bloqueado a tu perfil. Solicitudes al SAT solo del ano {{ $currentYear }} y por mes.
                        @endif
                    </p>
                </div>
            </section>

            <section class="workspace-panel cfdi-guide-panel">
                <div class="panel-header">
                    <div>
                        <h2>Flujo recomendado</h2>
                        <p>Trabaja como en el SAT, pero con menos pasos y con controles fiscales desde el inicio.</p>
                    </div>
                </div>
                <div class="cfdi-step-grid compact">
                    <article class="cfdi-step active">
                        <span>1</span>
                        <strong>Elige tipo</strong>
                        <p>Usa la pestaña Emitidos o Recibidos segun lo que necesitas consultar.</p>
                    </article>
                    <article class="cfdi-step {{ session('sat_authenticated') ? 'done' : 'error' }}">
                        <span>2</span>
                        <strong>Valida SAT</strong>
                        <p>{{ session('sat_authenticated') ? 'Tu e.firma ya esta conectada.' : 'Conecta tu e.firma desde Inicio antes de solicitar.' }}</p>
                    </article>
                    <article class="cfdi-step pending">
                        <span>3</span>
                        <strong>Solicita y descarga</strong>
                        <p>El sistema guarda la solicitud y muestra el estado cuando SAT libere paquetes.</p>
                    </article>
                </div>
            </section>

            <section class="cfdi-flow-shell">
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
                                <p>Selecciona RFC, fechas y tipo {{ $tab['type'] }}. En modo gratuito el RFC queda bloqueado y el rango maximo es 1 mes.</p>
                            </article>
                            <article class="cfdi-step {{ session('sat_authenticated') ? 'done' : 'error' }}">
                                <span>2</span>
                                <strong>Autenticacion SAT</strong>
                                <p>{{ session('sat_authenticated') ? 'Sesion SAT validada correctamente.' : 'Primero valida tu e.firma en Inicio.' }}</p>
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
                            <div class="cfdi-status processing">Procesando informacion</div>
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
                    ['label' => 'IVA trasladado', 'value' => '$'.number_format($metrics['iva_trasladado'], 2), 'note' => 'Base para declaracion'],
                    ['label' => 'CFDI recibidos', 'value' => number_format($metrics['recibidos_count']), 'note' => 'Comprobantes recibidos del periodo'],
                    ['label' => 'Gastos detectados', 'value' => '$'.number_format($metrics['recibidos_total'], 2), 'note' => 'Egresos deducibles por revisar'],
                    ['label' => 'IVA acreditable', 'value' => '$'.number_format($metrics['iva_acreditable'], 2), 'note' => 'Base para revision fiscal'],
                ] as $metric)
                    <article class="fiscal-kpi">
                        <span>{{ $metric['label'] }}</span>
                        <strong>{{ $metric['value'] }}</strong>
                        <small>{{ $metric['note'] }}</small>
                    </article>
                @endforeach
            </section>

            <section class="workspace-panel">
                <div class="panel-header">
                    <div>
                        <h2>Filtros CFDI</h2>
                        <p>La diferenciacion entre emitidos y recibidos se controla aqui.</p>
                    </div>
                </div>

                <form class="cfdi-filter-panel" method="get" action="{{ url('/cfdi') }}" data-free-mode="{{ $canUseMultipleRfcs ? '0' : '1' }}">
                    <div>
                        <label for="tipo">Tipo</label>
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
                    <button class="btn btn-primary" type="submit">Aplicar filtros</button>
                </form>
            </section>

            <section class="workspace-panel">
                <div class="panel-header">
                    <h2>Comprobantes CFDI</h2>
                    <a href="{{ url('/descargas/nueva') }}">Nueva descarga</a>
                </div>
                <div class="table-responsive">
                    <table class="fiscal-table">
                        <thead>
                        <tr>
                            <th>UUID</th>
                            <th>Emisor</th>
                            <th>Receptor</th>
                            <th class="text-end">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-4">{{ __('app.cfdi.empty') }}</td>
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
