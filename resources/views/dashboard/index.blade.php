@extends('layouts.app', [
    'title' => 'Dashboard fiscal | ContaPro',
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
                <a class="active" href="{{ url('/dashboard') }}"><span>▦</span>Dashboard</a>
                <a href="{{ url('/cfdi?tipo=emitidos') }}"><span>▤</span>CFDI Emitidos</a>
                <a href="{{ url('/cfdi?tipo=recibidos') }}"><span>▥</span>CFDI Recibidos</a>
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
                    <h1>Dashboard</h1>
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
                        <p>Ya puedes iniciar tus procesos de descarga y análisis CFDI.</p>
                    </div>
                </section>
            @endif

            <section class="orientation-box">
                <div>
                    <strong>Resumen general</strong>
                    <p>RFC activo: <b>{{ $profile?->rfc ?? 'Pendiente' }}</b> · Periodo: <b>{{ ucfirst($periodLabel) }}</b> · Tipo: <b>{{ $profile?->user_type ?? 'Pendiente' }}</b></p>
                </div>
                <a class="btn btn-primary" href="{{ url('/descargas/nueva') }}">Descargar CFDI</a>
            </section>

            <section class="fiscal-kpi-grid" aria-label="Indicadores fiscales">
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
                        <h2>Acciones rápidas</h2>
                        <p>Inicia las tareas frecuentes sin buscar entre módulos.</p>
                    </div>
                </div>
                <div class="quick-actions-grid">
                    <a href="{{ url('/descargas/nueva') }}">
                        <strong>Descargar CFDI</strong>
                        <span>{{ $canUseMultipleRfcs ? 'Usa tu RFC o clientes registrados.' : 'Modo gratuito: RFC de tu perfil.' }}</span>
                    </a>
                    <a href="{{ url('/dashboard') }}">
                        <strong>Cambiar periodo</strong>
                        <span>Próximamente: filtros por mes y ejercicio fiscal.</span>
                    </a>
                    <a href="{{ url('/cfdi') }}">
                        <strong>Ir a reportes</strong>
                        <span>Revisa emitidos, recibidos, montos e IVA.</span>
                    </a>
                </div>
            </section>

            <section class="dashboard-sat-intro" aria-labelledby="satDownloadTitle">
                <div class="dashboard-sat-header">
                    <h2 id="satDownloadTitle">Descarga Masiva de XMLs del SAT</h2>
                    <p>Utiliza tu e.firma (FIEL) para descargar tus comprobantes fiscales directamente del SAT.</p>
                </div>

                <div class="sat-process-grid">
                    <article>
                        <span>🛡</span>
                        <div>
                            <strong>1. Autenticación</strong>
                            <p>Carga tu certificado (.cer) y llave privada (.key) con tu contraseña para autenticarte con el SAT.</p>
                        </div>
                    </article>
                    <article>
                        <span>▤</span>
                        <div>
                            <strong>2. Solicitud</strong>
                            <p>Selecciona el rango de fechas y el tipo de comprobantes que deseas descargar.</p>
                        </div>
                    </article>
                    <article>
                        <span>⇩</span>
                        <div>
                            <strong>3. Descarga</strong>
                            <p>Espera a que el SAT procese tu solicitud y descarga los paquetes ZIP con tus XMLs.</p>
                        </div>
                    </article>
                </div>

                <div class="sat-requirements">
                    <div class="sat-warning-icon">!</div>
                    <div>
                        <strong>Límites y requisitos del SAT</strong>
                        <ul>
                            <li><span>✓</span><b>Rango de fechas:</b> máximo 1 mes por solicitud SAT.</li>
                            <li><span>✓</span><b>Antigüedad:</b> hasta 6 años de historial disponible.</li>
                            <li><span>✓</span><b>Tiempo de procesamiento:</b> de minutos a horas según volumen.</li>
                            <li><span>✓</span><b>Token de sesión:</b> válido por 5 minutos y se renueva durante el flujo.</li>
                            <li><span>✓</span><b>Solicitudes simultáneas:</b> máximo 2 solicitudes en proceso a la vez.</li>
                        </ul>
                    </div>
                </div>

                <a class="sat-start-link" href="#efirma-panel">→ Inicia sesión con tu e.firma para comenzar</a>
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

                <div class="efirma-form-grid">
                    <div class="efirma-field full">
                        <label for="dashboard_cer">Certificado (.cer)</label>
                        <label class="file-drop" for="dashboard_cer">
                            <span>▤</span>
                            <strong>Selecciona tu archivo .cer</strong>
                            <small>Haz clic para buscar el archivo</small>
                        </label>
                        <input id="dashboard_cer" type="file" accept=".cer">
                    </div>

                    <div class="efirma-field full">
                        <label for="dashboard_key">Llave privada (.key)</label>
                        <label class="file-drop" for="dashboard_key">
                            <span>⚿</span>
                            <strong>Selecciona tu archivo .key</strong>
                            <small>Haz clic para buscar el archivo</small>
                        </label>
                        <input id="dashboard_key" type="file" accept=".key">
                    </div>

                    <div class="efirma-field full">
                        <label for="dashboard_efirma_password">Contraseña de la llave privada</label>
                        <input id="dashboard_efirma_password" type="password" placeholder="Ingresa tu contraseña" autocomplete="off">
                    </div>

                    <button class="btn btn-outline-primary" type="button">Limpiar</button>
                    <a class="btn btn-primary" href="{{ url('/descargas/nueva') }}">Validar e.firma</a>
                </div>
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
                            ['Conecta tu e.firma', 'Carga tu certificado (.cer), llave privada (.key) e ingresa tu contraseña. Tu e.firma se valida localmente y se usa solo para firmar solicitudes al SAT.'],
                            ['Crea una solicitud', 'Selecciona el rango de fechas, tipo de comprobantes emitidos o recibidos y formato de descarga.'],
                            ['Espera el procesamiento', 'El SAT procesa tu solicitud. Puedes volver después; el tiempo varía según el volumen de comprobantes.'],
                            ['Verifica el estado', 'Consulta periódicamente si la solicitud ya está lista. El estado cambiará cuando los paquetes estén disponibles.'],
                            ['Descarga tus archivos', 'Cuando la solicitud esté lista, descarga los paquetes ZIP para conservar tus XML.'],
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
                        <h3>⚠ Consideraciones importantes</h3>
                        @foreach ([
                            ['Límite de tiempo del token', 'Tu sesión SAT tiene un token válido por 5 minutos. Se renueva automáticamente mientras usas la herramienta.'],
                            ['Máximo 1 mes por solicitud', 'El SAT solo permite solicitar comprobantes dentro de un rango de 1 mes. Para periodos más largos, crea solicitudes separadas.'],
                            ['Solicitudes simultáneas', 'El SAT permite máximo 2 solicitudes en proceso al mismo tiempo por RFC.'],
                            ['Expiración de paquetes', 'Los paquetes listos para descarga tienen vigencia limitada. Descárgalos antes de que expiren.'],
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
                    ['Seguridad de tu e.firma:', 'Tu e.firma (FIEL) se procesa de forma segura. Los archivos .cer y .key se utilizan únicamente para firmar las solicitudes al SAT.'],
                    ['Responsabilidad del usuario:', 'El usuario es responsable del uso correcto de su e.firma y de la confidencialidad de sus credenciales. No compartas tu llave privada ni tu contraseña con terceros.'],
                    ['Datos fiscales:', 'Los comprobantes descargados son documentos oficiales emitidos o recibidos por el contribuyente. La información contenida en ellos es confidencial y debe manejarse conforme a las leyes aplicables.'],
                    ['Disponibilidad del servicio:', 'La disponibilidad del servicio de descarga masiva depende del SAT. En ocasiones puede existir lentitud o interrupciones temporales por mantenimiento o alta demanda.'],
                ] as $notice)
                    <p><span>ⓘ</span><b>{{ $notice[0] }}</b> {{ $notice[1] }}</p>
                @endforeach
            </section>

            <section class="workspace-panel">
                <div class="panel-header">
                    <h2>Actividad reciente de CFDI</h2>
                    <a href="{{ url('/cfdi') }}">Ver todos</a>
                </div>
                <div class="table-responsive">
                    <table class="fiscal-table">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>RFC</th>
                            <th>Concepto</th>
                            <th class="text-end">Total</th>
                            <th>Estatus</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ([
                            ['fecha' => 'Pendiente', 'tipo' => 'Emitido', 'rfc' => 'Sin RFC', 'concepto' => 'Descarga inicial pendiente', 'total' => '$0.00', 'estatus' => 'Sin datos'],
                            ['fecha' => 'Pendiente', 'tipo' => 'Recibido', 'rfc' => 'Sin RFC', 'concepto' => 'Agrega un cliente para iniciar', 'total' => '$0.00', 'estatus' => 'Sin datos'],
                        ] as $row)
                            <tr>
                                <td>{{ $row['fecha'] }}</td>
                                <td>{{ $row['tipo'] }}</td>
                                <td>{{ $row['rfc'] }}</td>
                                <td>{{ $row['concepto'] }}</td>
                                <td class="text-end">{{ $row['total'] }}</td>
                                <td><span class="status-pill">{{ $row['estatus'] }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
@endsection
