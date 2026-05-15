@extends('layouts.app', [
    'title' => 'Descargar CFDI emitidos y recibidos del SAT | ContaPro',
    'metaDescription' => 'Herramienta web para descargar CFDI emitidos y recibidos del SAT, ordenar información fiscal y preparar declaraciones de personas físicas con menos trabajo manual.',
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
            "description": "Herramienta web para contadores en México que permite descargar CFDI emitidos y recibidos del SAT, ordenarlos y preparar datos fiscales para declaraciones de personas físicas.",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "MXN"
            },
            "areaServed": "MX"
        }
    </script>
@endpush

@section('content')
    <section class="home-hero">
        <div class="container">
            <div class="home-hero-grid">
                <div>
                    <span class="eyebrow">Herramienta web para contadores en México</span>
                    <h1>Descarga y ordena los CFDI de tus clientes en minutos, no en horas</h1>
                    <p class="hero-subtitle">CFDI emitidos y recibidos del SAT, listos para declarar, sin trabajo manual.</p>
                    <div class="hero-actions">
                        <a class="btn btn-primary btn-lg" href="{{ url('/registro') }}">Probar herramienta</a>
                        <a class="btn btn-outline-primary btn-lg" href="#como-funciona">Ver cómo funciona</a>
                    </div>
                    <p class="hero-trust">Pensado para declaraciones mensuales, anual de personas físicas y revisión fiscal recurrente.</p>
                </div>

                <div class="fiscal-preview" aria-label="Vista previa de organización de CFDI">
                    <div class="preview-header">
                        <strong>Preparación mensual</strong>
                        <span>CFDI listos</span>
                    </div>
                    <div class="preview-metrics">
                        <div><span>Emitidos</span><strong>126</strong></div>
                        <div><span>Recibidos</span><strong>384</strong></div>
                        <div><span>IVA</span><strong>$42,810</strong></div>
                    </div>
                    <div class="preview-table">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="trust-strip">
        <div class="container">
            <span>Descargar CFDI</span>
            <span>CFDI emitidos y recibidos</span>
            <span>Descarga masiva CFDI</span>
            <span>Declaraciones personas físicas</span>
        </div>
    </section>

    <section class="home-section problem-section">
        <div class="container narrow">
            <span class="eyebrow">El problema</span>
            <h2>El trabajo más pesado del contador no es calcular, es preparar la información.</h2>
            <p>Entrar al SAT, descargar CFDI uno por uno, revisar errores y clasificar manualmente consume tiempo que debería usarse en análisis, atención al cliente y cierre de declaraciones.</p>
        </div>
    </section>

    <section class="home-section solution-section" id="como-funciona">
        <div class="container">
            <div class="split-grid">
                <div>
                    <span class="eyebrow">La solución</span>
                    <h2>ContaPro prepara la información fiscal antes de que empieces a declarar</h2>
                    <p>La herramienta descarga CFDI emitidos y recibidos del SAT, los organiza automáticamente, extrae datos fiscales clave y los deja listos para revisión.</p>
                    <a class="btn btn-primary" href="{{ url('/registro') }}">Probar herramienta</a>
                </div>
                <div class="steps-card">
                    <div><strong>1</strong><span>El contador entra a su cuenta</span></div>
                    <div><strong>2</strong><span>Selecciona cliente y periodo</span></div>
                    <div><strong>3</strong><span>Consulta CFDI ordenados para declarar</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-section benefits-section">
        <div class="container">
            <div class="section-heading centered">
                <span class="eyebrow">Beneficios clave</span>
                <h2>Menos operación, más criterio contable</h2>
            </div>
            <div class="benefits-grid">
                <article>
                    <h3>Ahorro de tiempo operativo</h3>
                    <p>Reduce tareas repetitivas antes de la declaración mensual.</p>
                </article>
                <article>
                    <h3>Menos errores y omisiones</h3>
                    <p>Trabaja con información CFDI ordenada y fácil de revisar.</p>
                </article>
                <article>
                    <h3>Declaraciones más rápidas</h3>
                    <p>Ten emitidos, recibidos y datos fiscales en un mismo flujo.</p>
                </article>
                <article>
                    <h3>Mejor servicio al cliente</h3>
                    <p>Entrega respuestas más claras sin perder horas preparando datos.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="value-strip">
        <div class="container">
            <div>
                <strong>Hasta 75%</strong>
                <span>menos tiempo en preparación mensual</span>
            </div>
            <div>
                <strong>De horas a minutos</strong>
                <span>por contribuyente cuando la información está ordenada</span>
            </div>
        </div>
    </section>

    <section class="home-section audience-section" id="para-quien">
        <div class="container">
            <div class="section-heading centered">
                <span class="eyebrow">Para quién es</span>
                <h2>Diseñado para quienes atienden declaraciones de personas físicas</h2>
            </div>
            <div class="audience-grid compact">
                <article>
                    <h3>Contadores de personas físicas</h3>
                    <p>Para ordenar CFDI antes de pagos provisionales y anual.</p>
                </article>
                <article>
                    <h3>Despachos pequeños y medianos</h3>
                    <p>Para estandarizar el trabajo de varios clientes.</p>
                </article>
                <article>
                    <h3>Contadores independientes</h3>
                    <p>Para atender más clientes sin duplicar carga operativa.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="final-cta-section">
        <div class="container">
            <div class="final-cta-card">
                <div>
                    <h2>Empieza a ahorrar tiempo hoy</h2>
                    <p>Prueba ContaPro y prepara CFDI emitidos y recibidos del SAT con una herramienta hecha para contadores.</p>
                </div>
                <a class="btn btn-primary btn-lg" href="{{ url('/registro') }}">Probar herramienta</a>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container">
            <div>
                <strong>ContaPro</strong>
                <p>Descarga y organización de CFDI para contadores en México.</p>
            </div>
            <nav aria-label="Enlaces principales">
                <a href="{{ url('/registro') }}">Registro</a>
                <a href="{{ url('/login') }}">Entrar</a>
                <a href="#como-funciona">Cómo funciona</a>
            </nav>
        </div>
    </footer>
@endsection
