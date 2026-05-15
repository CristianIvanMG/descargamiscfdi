@extends('layouts.app', ['title' => __('app.descarga.status_title')])

@section('content')
    <section class="app-shell py-4" x-data="contadorMxDescarga('{{ url('/api/descargas/'.$descarga->getKey().'/estado') }}')" x-init="start()">
        <div class="container">
            <div class="panel">
                <h1 class="h4 fw-bold">{{ __('app.descarga.status_title') }}</h1>
                <p class="mb-1">{{ __('app.descarga.status') }}: <strong x-text="estado"></strong></p>
                <p class="mb-0">{{ __('app.descarga.total_cfdi') }}: <strong x-text="totalCfdi"></strong></p>
                <p class="text-danger mb-0" x-text="mensajeError"></p>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/descarga.js') }}"></script>
@endpush
