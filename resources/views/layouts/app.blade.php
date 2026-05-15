<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $pageTitle = $title ?? config('app.name');
        $pageDescription = $metaDescription ?? 'Descarga masiva de XML CFDI del SAT en linea. ContaPro organiza facturas emitidas y recibidas, reportes fiscales, validacion y seguridad para e.firma.';
        $pageCanonical = $canonical ?? url('/');
        $pageImage = $metaImage ?? asset('css/app.css');
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="{{ $pageCanonical }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="ContaPro">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="theme-color" content="#006341">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%23006341'/%3E%3Cpath d='M18 34c0-10 6-17 16-17 5 0 9 2 12 5l-5 6c-2-2-4-3-7-3-5 0-8 4-8 9s3 9 8 9c3 0 6-1 8-3l5 6c-3 3-8 5-13 5-10 0-16-7-16-17Z' fill='white'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body>
    <nav class="navbar navbar-expand-lg border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">ContaPro</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="{{ __('app.nav.toggle') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="mainNav" class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#herramientas') }}">Herramientas</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#funcionalidades') }}">Funcionalidades</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#seguridad') }}">Seguridad</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/#faq') }}">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Entrar</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-lg-2" href="{{ url('/registro') }}">Crear cuenta</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
