@extends('layouts.app', ['title' => __('app.rfc.title')])

@section('content')
    <section class="app-shell py-4">
        <div class="container">
            <div class="mb-4">
                <h1 class="h3 fw-bold mb-1">{{ __('app.rfc.heading') }}</h1>
                <p class="text-secondary mb-0">{{ __('app.rfc.subtitle') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            <form class="panel mb-4" method="post" action="{{ url('/rfcs') }}">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="rfc">{{ __('app.rfc.rfc') }}</label>
                        <input id="rfc" class="form-control" name="rfc" maxlength="13" required data-rfc-mask aria-describedby="rfcFeedback">
                        <div id="rfcFeedback" class="form-text rfc-feedback" data-rfc-feedback="rfc">Formato: 12 o 13 caracteres con homoclave.</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="razon_social">{{ __('app.rfc.business_name') }}</label>
                        <input id="razon_social" class="form-control" name="razon_social" required>
                    </div>
                    <div class="col-12 col-md-2">
                        <button class="btn btn-primary w-100" type="submit">{{ __('app.rfc.add') }}</button>
                    </div>
                </div>
            </form>

            <div class="panel text-secondary">{{ __('app.rfc.empty') }}</div>
        </div>
    </section>
@endsection
