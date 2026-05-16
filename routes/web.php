<?php

use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CfdiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DescargaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\RfcController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/favicon.ico', fn () => response(status: 204));
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
Route::view('/registro', 'auth.register')->name('register');
Route::post('/registro', [RegistroController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
Route::get('/registro/confirmacion', [RegistroController::class, 'notice'])->name('verification.notice');
Route::post('/registro/reenviar-confirmacion', [RegistroController::class, 'resend'])->middleware('throttle:3,10')->name('verification.resend');
Route::get('/registro/confirmar/{id}/{hash}', [RegistroController::class, 'confirm'])->middleware('signed')->name('verification.confirm');
Route::view('/registro/confirmado', 'auth.confirmed')->name('verification.confirmed');
Route::view('/recuperar', 'auth.forgot-password')->name('password.request');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('profile');
    Route::post('/perfil', [PerfilController::class, 'update'])->name('profile.update');
    Route::get('/dashboard', DashboardController::class)->middleware(App\Http\Middleware\EnsureProfileIsComplete::class)->name('dashboard');

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
});

Route::post('/webhooks/conekta', [WebhookController::class, 'conekta'])->name('webhooks.conekta');
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');
