@extends('layouts.app', [
    'title' => 'CFDI | ContaPro',
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
                <a href="{{ url('/dashboard') }}"><span>▦</span>Inicio</a>
                <a class="active" href="{{ url('/cfdi') }}"><span>▤</span>CFDI</a>
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
                    <h1>CFDI</h1>
                    <p>Panel operativo para emitidos, recibidos, montos e IVA.</p>
                </div>
                <div class="workspace-actions">
                    <a class="btn btn-primary" href="{{ session('sat_authenticated') ? url('/descargas/nueva') : url('/dashboard#efirma-panel') }}">Descargar CFDI</a>
                </div>
            </header>

            @unless (session('sat_authenticated'))
                <section class="sat-connection-status is-disconnected cfdi-blocker">
                    <strong>Conexión con el SAT no establecida</strong>
                    <p>Para solicitar descargas reales primero valida tu e.firma en Inicio. Puedes revisar la estructura de CFDI, pero no iniciar solicitudes hasta conectar con SAT.</p>
                    <a class="btn btn-primary" href="{{ url('/dashboard#efirma-panel') }}">Conectar e.firma</a>
                </section>
            @endunless

            <section class="orientation-box">
                <div>
                    <strong>CFDI – Descarga y gestión</strong>
                    <p>Consulta y descarga CFDI emitidos y recibidos del SAT.</p>
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
                        <div class="cfdi-step-grid">
                            <article class="cfdi-step active">
                                <span>1</span>
                                <strong>Configura</strong>
                                <p>Selecciona RFC, fecha inicio, fecha fin y tipo {{ $tab['type'] }}. Se recomienda descargar por mes.</p>
                            </article>
                            <article class="cfdi-step {{ session('sat_authenticated') ? 'done' : 'error' }}">
                                <span>2</span>
                                <strong>Autenticación SAT</strong>
                                <p>{{ session('sat_authenticated') ? 'Sesión SAT validada correctamente.' : 'Primero valida tu e.firma en Inicio.' }}</p>
                            </article>
                            <article class="cfdi-step pending">
                                <span>3</span>
                                <strong>Solicita</strong>
                                <p>Se crea la solicitud SAT y se guarda el RequestId para historial.</p>
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

                        <form class="cfdi-request-panel" method="get" action="{{ url('/cfdi') }}">
                            <input type="hidden" name="tipo" value="{{ $tab['type'] }}">
                            <div>
                                <label>RFC</label>
                                <input name="rfc" value="{{ $filters['rfc'] }}" placeholder="RFC del perfil o cliente" data-rfc-mask>
                            </div>
                            <div>
                                <label>Fecha inicio</label>
                                <input name="fecha_inicio" type="date" value="{{ $filters['fecha_inicio'] }}">
                            </div>
                            <div>
                                <label>Fecha fin</label>
                                <input name="fecha_fin" type="date" value="{{ $filters['fecha_fin'] }}">
                            </div>
                            <div>
                                <label>Tipo CFDI</label>
                                <input value="{{ ucfirst($tab['type']) }}" readonly>
                            </div>
                            <button class="btn btn-primary" type="button" @disabled(! session('sat_authenticated'))>Solicitar descarga</button>
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

            <section class="workspace-panel">
                <div class="panel-header">
                    <div>
                        <h2>Filtros CFDI</h2>
                        <p>La diferenciación entre emitidos y recibidos se controla aquí.</p>
                    </div>
                </div>

                <div class="cfdi-filter-tabs" role="tablist" aria-label="Filtros CFDI">
                    <a @class(['active' => $filters['tipo'] === 'todos']) href="{{ url('/cfdi?tipo=todos') }}">Todos</a>
                    <a @class(['active' => $filters['tipo'] === 'emitidos']) href="{{ url('/cfdi?tipo=emitidos') }}">Emitidos</a>
                    <a @class(['active' => $filters['tipo'] === 'recibidos']) href="{{ url('/cfdi?tipo=recibidos') }}">Recibidos</a>
                </div>

                <form class="cfdi-filter-panel" method="get" action="{{ url('/cfdi') }}">
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
                        <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ $filters['fecha_inicio'] }}">
                    </div>
                    <div>
                        <label for="fecha_fin">Fecha final</label>
                        <input id="fecha_fin" name="fecha_fin" type="date" value="{{ $filters['fecha_fin'] }}">
                    </div>
                    <div>
                        <label for="rfc">RFC</label>
                        <input id="rfc" name="rfc" value="{{ $filters['rfc'] }}" placeholder="Filtrar por RFC" data-rfc-mask>
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

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const target = tab.dataset.cfdiTab;
                    tabs.forEach((item) => item.classList.toggle('active', item === tab));
                    panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.cfdiPanel === target));
                });
            });
        })();
    </script>
@endsection
