@extends('layouts.app', [
    'title' => 'Descarga Masiva de XML Gratis | CFDI SAT en línea | ContaPro',
    'metaDescription' => 'Descarga masiva de XML CFDI del SAT gratis en línea. ContaPro ayuda a contadores, despachos y empresas a organizar emitidos, recibidos, metadata, reportes Excel/PDF y validación de CFDI sin instalar programas.',
    'canonical' => url('/'),
])

@push('head')
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "ContaPro",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Web",
            "url": "{{ url('/') }}",
            "description": "Herramienta web para descarga masiva de XML CFDI del SAT, organización de comprobantes, reportes fiscales y validación de facturas electrónicas.",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "MXN"
            },
            "areaServed": "MX"
        }
    </script>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {"@type":"Question","name":"¿ContaPro descarga XML del SAT gratis?","acceptedAnswer":{"@type":"Answer","text":"Sí. ContaPro inicia con una herramienta gratuita para solicitar y organizar CFDI del SAT desde el navegador."}},
                {"@type":"Question","name":"¿Tengo que instalar un programa?","acceptedAnswer":{"@type":"Answer","text":"No. ContaPro funciona en línea desde descargamiscfdi.com y está diseñado para navegador."}},
                {"@type":"Question","name":"¿La llave .key se sube al servidor?","acceptedAnswer":{"@type":"Answer","text":"No. El flujo está diseñado para procesar la e.firma en el navegador y evitar almacenar la llave privada como archivo en el servidor."}}
            ]
        }
    </script>
@endpush

@section('content')
    @php
        $audiences = [
            ['title' => 'Contadores independientes', 'body' => 'Descarga emitidos y recibidos de varios RFC sin perder el control de cada contribuyente.'],
            ['title' => 'Despachos contables', 'body' => 'Centraliza clientes, periodos, reportes y tareas repetitivas en un flujo web.'],
            ['title' => 'Empresas en México', 'body' => 'Revisa facturas, IVA, proveedores y comprobantes cancelados desde un panel claro.'],
            ['title' => 'Auxiliares administrativos', 'body' => 'Sigue pasos simples sin entender configuraciones técnicas del SAT.'],
        ];

        $tools = [
            ['title' => 'Descarga masiva XML SAT', 'body' => 'Solicita CFDI emitidos y recibidos por RFC, tipo y periodo.'],
            ['title' => 'Metadata SAT', 'body' => 'Consulta información útil para detectar actividad sin abrir XML uno por uno.'],
            ['title' => 'Reporte Excel CFDI', 'body' => 'Prepara reportes contables para revisión, conciliación y auditoría.'],
            ['title' => 'Conversión XML a PDF', 'body' => 'Visualiza comprobantes en formato amigable para revisión interna.'],
            ['title' => 'Validación de CFDI', 'body' => 'Identifica comprobantes vigentes, cancelados o con estatus pendiente.'],
            ['title' => 'Alertas fiscales', 'body' => 'Base preparada para avisos de cancelación, EFOS y cambios relevantes.'],
        ];

        $features = [
            'Descarga de CFDI emitidos',
            'Descarga de CFDI recibidos',
            'Panel por RFC y periodo',
            'Reportes Excel y resúmenes',
            'Storage privado cifrado',
            'Cola de procesamiento SAT',
            'Validación de estatus CFDI',
            'Filtros por emisor y receptor',
            'Preparado para planes Pro',
        ];

        $faqs = [
            ['q' => '¿ContaPro es gratis?', 'a' => 'La entrada al producto se plantea con una herramienta gratuita para validar valor y uso. Los planes Pro agregan automatización, reportes avanzados, almacenamiento y operación por despacho.'],
            ['q' => '¿Sirve para descargar XML emitidos y recibidos?', 'a' => 'Sí. La arquitectura contempla solicitudes para CFDI emitidos y recibidos por RFC y periodo.'],
            ['q' => '¿Necesito instalar algo en Windows?', 'a' => 'No. ContaPro nace como plataforma web para evitar instaladores, licencias por computadora y dependencia de una sola PC.'],
            ['q' => '¿Qué pasa con mi e.firma?', 'a' => 'El flujo está diseñado para que la llave privada se procese en el navegador. La página comunica con claridad que la .key no debe almacenarse como archivo en el servidor.'],
            ['q' => '¿El SAT siempre entrega los XML al instante?', 'a' => 'No siempre. El SAT puede tardar dependiendo del volumen y disponibilidad del servicio. ContaPro muestra estado y avance para que el usuario no se quede adivinando.'],
            ['q' => '¿Puedo usarlo para varios RFC?', 'a' => 'Sí. La estructura de usuario, RFC y suscripción está preparada para manejar contribuyentes y despachos.'],
            ['q' => '¿Genera reportes en Excel o PDF?', 'a' => 'La arquitectura incluye servicios para reportes fiscales y conversión; se activarán conforme avancen las fases de base de datos y procesamiento.'],
            ['q' => '¿Mis XML quedan seguros?', 'a' => 'Los archivos se guardan fuera del directorio público y el storage privado está preparado para cifrado.'],
            ['q' => '¿Puedo entrar a un perfil privado?', 'a' => 'Sí. Los llamados de la landing llevan a registro y posteriormente al dashboard privado con descargas, CFDI, RFC y planes.'],
        ];
    @endphp

    <section class="landing-hero">
        <div class="container">
            <div class="landing-hero-grid">
                <div>
                    <span class="seo-badge">Descarga masiva XML SAT gratis</span>
                    <h1>Descarga tus XML CFDI del SAT en línea, sin instalar programas</h1>
                    <p class="landing-lead">
                        ContaPro ayuda a contadores, despachos y empresas en México a solicitar, organizar y revisar CFDI emitidos y recibidos desde un panel privado fácil de usar.
                    </p>
                    <div class="hero-keywords" aria-label="Beneficios principales">
                        <span>CFDI 4.0</span>
                        <span>XML SAT</span>
                        <span>Metadata</span>
                        <span>Excel/PDF</span>
                        <span>e.firma protegida</span>
                    </div>
                    <div class="hero-actions">
                        <a class="btn btn-primary btn-lg" href="{{ url('/registro') }}">Crear cuenta gratis</a>
                        <a class="btn btn-outline-primary btn-lg" href="#demo">Ver demo</a>
                    </div>
                    <p class="hero-note">Pensado para usuarios no técnicos: si sabes elegir un mes y tu RFC, puedes usarlo.</p>
                </div>

                <form class="landing-card" action="{{ url('/registro') }}" method="get" aria-label="Crear cuenta ContaPro">
                    <h2>Empieza gratis</h2>
                    <p>Recibe acceso al perfil privado para preparar tus descargas SAT.</p>
                    <label for="hero_email">Correo de trabajo</label>
                    <input id="hero_email" name="email" type="email" placeholder="tu@empresa.com" autocomplete="email" required>
                    <label for="hero_rfc">RFC principal</label>
                    <input id="hero_rfc" name="rfc" type="text" maxlength="13" placeholder="XAXX010101000" required>
                    <label for="hero_profile">Perfil</label>
                    <select id="hero_profile" name="perfil">
                        <option>Contador independiente</option>
                        <option>Despacho contable</option>
                        <option>Empresa</option>
                        <option>Contribuyente</option>
                    </select>
                    <button class="btn btn-primary btn-full" type="submit">Entrar a mi panel</button>
                    <span>Sin tarjeta. Sin instalador. Flujo web.</span>
                </form>
            </div>
        </div>
    </section>

    <section class="trust-bar" aria-label="Confianza y seguridad">
        <div class="container">
            <div class="trust-bar-inner">
                <span>100% navegador</span>
                <span>Storage privado</span>
                <span>Preparado para Web Service SAT</span>
                <span>e.firma no se almacena como archivo</span>
                <span>Laravel + MySQL</span>
            </div>
        </div>
    </section>

    <section class="seo-section why-free">
        <div class="container narrow">
            <span class="section-eyebrow">¿Por qué es gratis?</span>
            <h2>Porque primero queremos que compruebes que tus CFDI pueden ordenarse sin dolor</h2>
            <p>
                La descarga masiva de XML del SAT suele sentirse técnica, lenta y confusa. ContaPro inicia con una experiencia gratuita para que pruebes el flujo, entiendas el estado de tus solicitudes y veas tus comprobantes en un panel claro antes de contratar funciones avanzadas.
            </p>
        </div>
    </section>

    <section class="seo-section" id="para-quien">
        <div class="container">
            <div class="section-heading centered">
                <span class="section-eyebrow">¿Para quién es?</span>
                <h2>Diseñado para quien necesita XML sin perder la tarde</h2>
                <p>El contenido, los botones y los reportes se explican en lenguaje simple para que no dependas de una sola persona técnica.</p>
            </div>
            <div class="audience-grid">
                @foreach ($audiences as $audience)
                    <article class="audience-card">
                        <span class="card-dot" aria-hidden="true"></span>
                        <h3>{{ $audience['title'] }}</h3>
                        <p>{{ $audience['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="seo-section feature-highlight">
        <div class="container">
            <div class="feature-grid">
                <div class="dashboard-mockup" aria-label="Imagen del dashboard privado ContaPro">
                    <div class="mock-sidebar">
                        <strong>ContaPro</strong>
                        <span></span><span></span><span></span><span></span>
                    </div>
                    <div class="mock-content">
                        <div class="mock-top"></div>
                        <div class="mock-kpis">
                            <b>26</b><b>101</b><b>1,454</b><b>$17,237</b>
                        </div>
                        <div class="mock-table">
                            @for ($i = 0; $i < 9; $i++)
                                <span></span>
                            @endfor
                        </div>
                    </div>
                </div>
                <div>
                    <span class="section-eyebrow">Panel privado</span>
                    <h2>Después del registro entras a tu espacio de trabajo</h2>
                    <p>El flujo de fase 2 queda conectado al perfil privado: dashboard, RFC, descargas, CFDI, planes y webhooks. La landing vende; el panel opera.</p>
                    <ul class="check-list">
                        <li>Dashboard con emitidos, recibidos, IVA y actividad.</li>
                        <li>Descarga nueva con RFC, periodo, tipo y e.firma.</li>
                        <li>Listado CFDI con filtros para análisis fiscal.</li>
                        <li>Preparado para despachos con múltiples RFC.</li>
                    </ul>
                    <a class="btn btn-primary" href="{{ url('/dashboard') }}">Ver estructura del panel</a>
                </div>
            </div>
        </div>
    </section>

    <section class="seo-section" id="herramientas">
        <div class="container">
            <div class="section-heading centered">
                <span class="section-eyebrow">Herramientas adicionales</span>
                <h2>Todo lo que un contador espera alrededor de sus XML</h2>
            </div>
            <div class="tools-grid">
                @foreach ($tools as $tool)
                    <article class="tool-card">
                        <span class="tool-icon" aria-hidden="true"></span>
                        <h3>{{ $tool['title'] }}</h3>
                        <p>{{ $tool['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="proof-strip">
        <div class="container">
            <p>Mercado validado: los contadores ya buscan descarga masiva XML, reportes Excel, PDF, metadata y validación. ContaPro mejora el acceso con una experiencia web.</p>
        </div>
    </section>

    <section class="seo-section" id="funcionalidades">
        <div class="container">
            <div class="section-heading centered">
                <span class="section-eyebrow">Funcionalidades</span>
                <h2>9 módulos pensados para crecer sin rehacer el sistema</h2>
            </div>
            <div class="features-grid">
                @foreach ($features as $index => $feature)
                    <article class="feat-card">
                        <span>Función {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $feature }}</h3>
                        <p>Integrado a la arquitectura Laravel de ContaPro para operar desde un perfil privado.</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="seo-section demo-section" id="demo">
        <div class="container">
            <div class="section-heading centered">
                <span class="section-eyebrow">Demo</span>
                <h2>Ve el flujo antes de usarlo</h2>
                <p>El usuario entiende qué va a pasar: solicita, espera estado, descarga y consulta resultados.</p>
            </div>
            <div class="demo-player" role="button" tabindex="0" aria-label="Reproducir demo de ContaPro">
                <span class="play-button"></span>
                <strong>Demo del panel ContaPro</strong>
            </div>
        </div>
    </section>

    <section class="seo-section screenshots-section">
        <div class="container">
            <div class="section-heading centered">
                <span class="section-eyebrow">Screenshots</span>
                <h2>Una interfaz privada clara, parecida a una agenda de trabajo</h2>
            </div>
            <div id="screenshotsCarousel" class="carousel slide app-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach (['Dashboard fiscal', 'Descarga SAT', 'Listado CFDI'] as $index => $slide)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="screenshot-card">
                                <div class="screenshot-sidebar"></div>
                                <div class="screenshot-body">
                                    <h3>{{ $slide }}</h3>
                                    <div class="screenshot-lines">
                                        @for ($i = 0; $i < 10; $i++)
                                            <span></span>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#screenshotsCarousel" data-bs-slide="prev" aria-label="Anterior">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#screenshotsCarousel" data-bs-slide="next" aria-label="Siguiente">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </section>

    <section class="seo-section how-section">
        <div class="container">
            <div class="section-heading centered">
                <span class="section-eyebrow">Cómo funciona</span>
                <h2>De solicitud SAT a reportes entendibles</h2>
            </div>
            <div class="steps-grid">
                @foreach (['Crea tu cuenta', 'Agrega RFC y periodo', 'Firma en navegador', 'Consulta avance', 'Revisa CFDI', 'Exporta reportes'] as $index => $step)
                    <article class="step-card">
                        <span class="step-number">{{ $index + 1 }}</span>
                        <h3>{{ $step }}</h3>
                        <p>Un paso corto, visible y pensado para no perder contexto durante la descarga.</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="stats-strip">
        <div class="container">
            <div class="stats-grid">
                <div><strong>200k</strong><span>XML por solicitud preparada</span></div>
                <div><strong>24/7</strong><span>Acceso web</span></div>
                <div><strong>0</strong><span>Instaladores</span></div>
                <div><strong>3</strong><span>Pasos para iniciar</span></div>
            </div>
        </div>
    </section>

    <section class="seo-section security-section" id="seguridad">
        <div class="container">
            <div class="feature-grid">
                <div>
                    <span class="section-eyebrow">Seguridad</span>
                    <h2>La confianza se explica antes de pedir datos sensibles</h2>
                    <p>ContaPro debe ser transparente: qué se firma, qué se guarda, qué no se guarda y por qué el storage privado no está expuesto al público.</p>
                    <ul class="check-list">
                        <li>La .key se procesa en navegador con WebCrypto.</li>
                        <li>Los XML se guardan fuera del directorio público.</li>
                        <li>Sesiones cifradas, cookies seguras y headers HTTP.</li>
                        <li>Jobs de descarga separados del flujo visible del usuario.</li>
                    </ul>
                </div>
                <div class="security-visual" aria-label="Seguridad de e.firma y XML">
                    <span class="lock-ring"></span>
                    <strong>e.firma protegida</strong>
                    <p>Nunca diseñar una experiencia que normalice subir la llave privada sin explicación clara.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="seo-section faq-section" id="faq">
        <div class="container">
            <div class="section-heading centered">
                <span class="section-eyebrow">Preguntas frecuentes</span>
                <h2>Respuestas claras antes del registro</h2>
            </div>
            <div class="accordion faq-accordion" id="faqAccordion">
                @foreach ($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faq{{ $index }}">
                                {{ $faq['q'] }}
                            </button>
                        </h3>
                        <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">{{ $faq['a'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="final-register" id="registro">
        <div class="container">
            <div class="final-register-grid">
                <div>
                    <span class="section-eyebrow">CTA final</span>
                    <h2>Entra a tu perfil privado de ContaPro</h2>
                    <p>Desde ahí seguirá el flujo general de fase 2: dashboard, RFC, nueva descarga, estado de jobs, CFDI y planes.</p>
                </div>
                <form class="landing-card compact" action="{{ url('/registro') }}" method="get" aria-label="Registro final ContaPro">
                    <label for="final_email">Correo</label>
                    <input id="final_email" name="email" type="email" placeholder="tu@empresa.com" required>
                    <label for="final_rfc">RFC</label>
                    <input id="final_rfc" name="rfc" type="text" maxlength="13" placeholder="RFC a revisar" required>
                    <button class="btn btn-primary btn-full" type="submit">Crear perfil privado</button>
                </form>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container">
            <div>
                <strong>ContaPro</strong>
                <p>Descarga masiva de XML CFDI del SAT en línea para México.</p>
            </div>
            <nav aria-label="Enlaces de pie de página">
                <a href="{{ url('/registro') }}">Registro</a>
                <a href="{{ url('/login') }}">Login</a>
                <a href="#seguridad">Seguridad</a>
                <a href="#faq">FAQ</a>
            </nav>
        </div>
    </footer>
@endsection
