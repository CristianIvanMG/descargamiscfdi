@extends('layouts.app', ['title' => __('app.cfdi.detail_title')])

@section('content')
    <section class="app-shell py-4">
        <div class="container">
            <div class="panel">
                <h1 class="h4 fw-bold">{{ __('app.cfdi.detail_title') }}</h1>
                <p class="text-secondary mb-0">{{ $cfdi->uuid }}</p>
            </div>
        </div>
    </section>
@endsection
