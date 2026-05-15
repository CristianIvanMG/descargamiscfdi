@extends('layouts.app', ['title' => __('app.auth.forgot')])

@section('content')
    <section class="app-shell py-4">
        <div class="container">
            <div class="panel mx-auto auth-panel">
                <h1 class="h4 fw-bold">{{ __('app.auth.forgot') }}</h1>
            </div>
        </div>
    </section>
@endsection
