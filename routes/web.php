<?php

use App\Http\Controllers\CfdiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DescargaController;
use App\Http\Controllers\RfcController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/favicon.ico', fn () => response(status: 204));
Route::view('/login', 'auth.login')->name('login');
Route::view('/registro', 'auth.register')->name('register');
Route::view('/registro/confirmacion', 'auth.verify-email')->name('verification.notice');
Route::view('/registro/confirmado', 'auth.confirmed')->name('verification.confirmed');
Route::view('/recuperar', 'auth.forgot-password')->name('password.request');
Route::view('/perfil', 'perfil.index')->name('profile');

Route::get('/dashboard', DashboardController::class)->name('dashboard');

Route::prefix('descargas')->name('descargas.')->group(function (): void {
    Route::get('/nueva', [DescargaController::class, 'create'])->name('create');
    Route::post('/', [DescargaController::class, 'store'])->name('store');
    Route::get('/{descargaJob}/estado', [DescargaController::class, 'show'])->name('show');
});

Route::prefix('cfdi')->name('cfdi.')->group(function (): void {
    Route::get('/', [CfdiController::class, 'index'])->name('index');
    Route::get('/{cfdi}', [CfdiController::class, 'show'])->name('show');
});

Route::resource('rfcs', RfcController::class)->only(['index', 'store', 'destroy']);

Route::get('/suscripcion/planes', [SuscripcionController::class, 'plans'])->name('suscripcion.planes');
Route::post('/suscripcion/checkout', [SuscripcionController::class, 'checkout'])->name('suscripcion.checkout');

Route::post('/webhooks/conekta', [WebhookController::class, 'conekta'])->name('webhooks.conekta');
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');
