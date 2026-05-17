<?php

use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CfdiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DescargaController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\RfcController;
use App\Http\Controllers\SatAuthenticationController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->middleware(App\Http\Middleware\RedirectAuthenticatedToDashboard::class)->name('home');
Route::get('/favicon.ico', fn () => response(status: 204));
Route::get('/suscripcion/planes', [SuscripcionController::class, 'plans'])->name('suscripcion.planes');
Route::get('/login', [LoginController::class, 'create'])
    ->middleware(App\Http\Middleware\RedirectAuthenticatedToDashboard::class)
    ->name('login');
Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
Route::view('/registro', 'auth.register')
    ->middleware(App\Http\Middleware\RedirectAuthenticatedToDashboard::class)
    ->name('register');
Route::post('/registro', [RegistroController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
Route::get('/registro/confirmacion', [RegistroController::class, 'notice'])->name('verification.notice');
Route::post('/registro/reenviar-confirmacion', [RegistroController::class, 'resend'])->middleware('throttle:3,10')->name('verification.resend');
Route::get('/registro/confirmar/{id}/{hash}', [RegistroController::class, 'confirm'])->middleware('signed')->name('verification.confirm');
Route::view('/registro/confirmado', 'auth.confirmed')->name('verification.confirmed');
Route::view('/recuperar', 'auth.forgot-password')->name('password.request');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/perfil', [PerfilController::class, 'edit'])
        ->middleware(App\Http\Middleware\RememberPrivateRoute::class)
        ->name('profile');
    Route::post('/perfil', [PerfilController::class, 'update'])->name('profile.update');

    Route::middleware([
        App\Http\Middleware\EnsureProfileIsComplete::class,
        App\Http\Middleware\RememberPrivateRoute::class,
    ])->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::post('/sat/autenticacion', [SatAuthenticationController::class, 'store'])
            ->middleware('throttle:3,1')
            ->name('sat.auth');

        Route::prefix('cfdi')->name('cfdi.')->group(function (): void {
            Route::get('/', [CfdiController::class, 'index'])->name('index');
            Route::get('/exportar', [CfdiController::class, 'export'])->name('export');
            Route::get('/{cfdi}', [CfdiController::class, 'show'])->name('show');
        });

        Route::middleware(App\Http\Middleware\EnsureSatIsAuthenticated::class)->group(function (): void {
            Route::prefix('descargas')->name('descargas.')->group(function (): void {
                Route::get('/nueva', [DescargaController::class, 'create'])->name('create');
                Route::post('/', [DescargaController::class, 'store'])->middleware('throttle:10,60')->name('store');
                Route::get('/{descargaJob}/estado', [DescargaController::class, 'show'])->name('show');
                Route::delete('/{descargaJob}', [DescargaController::class, 'destroy'])->name('destroy');
            });

            Route::middleware(App\Http\Middleware\EnsureMembershipAllowsAction::class)->group(function (): void {
                Route::resource('rfcs', RfcController::class)->only(['index', 'store', 'destroy']);
            });
        });

        Route::post('/suscripcion/checkout', [SuscripcionController::class, 'checkout'])->name('suscripcion.checkout');
        Route::get('/suscripcion/mi', [SuscripcionController::class, 'account'])->name('suscripcion.account');
        Route::get('/subscriptions/current', [SuscripcionController::class, 'current'])->name('subscriptions.current');
        Route::get('/subscriptions/plans', [SuscripcionController::class, 'plansJson'])->name('subscriptions.plans');
        Route::post('/subscriptions/create', [SuscripcionController::class, 'create'])->name('subscriptions.create');
        Route::post('/subscriptions/change', [SuscripcionController::class, 'change'])->name('subscriptions.change');
        Route::post('/subscriptions/cancel', [SuscripcionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::view('/suscripcion/upgrade', 'suscripcion.upgrade')->name('suscripcion.upgrade');
        Route::get('/donaciones/mercadopago', [DonationController::class, 'mercadoPago'])->name('donaciones.mercadopago');
        Route::get('/pagos/mercadopago/retorno', [PagoController::class, 'mercadoPagoReturn'])->name('pagos.mercadopago.return');
        Route::get('/historial', HistorialController::class)->name('historial.index');
    });
});

Route::post('/webhooks/conekta', [WebhookController::class, 'conekta'])->name('webhooks.conekta');
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');
Route::post('/webhooks/mercadopago', [PagoController::class, 'mercadoPagoWebhook'])->name('webhooks.mercadopago');
