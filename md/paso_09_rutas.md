# PASO 9 — RUTAS · web.php y api.php
## ContadorMx · xml.contadormx.net

---

## RUTAS — routes/web.php y routes/api.php

```php
// routes/web.php

use App\Http\Controllers\{
    DescargaController, CfdiController, DashboardController,
    RfcController, SuscripcionController, WebhookController
};
use App\Http\Middleware\RequierePlan;

// Públicas
Route::get('/', fn() => view('welcome'));
Route::get('/precios', fn() => view('suscripcion.planes'))->name('suscripcion.planes');

// Auth (Laravel Breeze / Jetstream)
require __DIR__.'/auth.php';

// Autenticadas — plan GRATIS en adelante
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // RFCs
    Route::resource('rfcs', RfcController::class)->only(['index','store','destroy']);
    
    // Descarga — disponible en todos los planes (1 RFC en gratis)
    Route::get('/descarga',         [DescargaController::class, 'nueva'])->name('descarga.nueva');
    Route::post('/descarga',        [DescargaController::class, 'iniciar'])->name('descarga.iniciar');
    Route::get('/descarga/{job}',   [DescargaController::class, 'estado'])->name('descarga.estado');
    
    // CFDI — listado y descarga de archivo
    Route::get('/cfdis',            [CfdiController::class, 'index'])->name('cfdis.index');
    Route::get('/cfdis/{cfdi}',     [CfdiController::class, 'show'])->name('cfdis.show');
    
    // Descarga XML/PDF — solo Pro y Despacho
    Route::get('/cfdis/{cfdi}/xml', [CfdiController::class, 'descargarXml'])
        ->middleware(RequierePlan::class . ':pro,despacho')
        ->name('cfdis.xml');
    
    // Dashboard fiscal — solo Pro y Despacho
    Route::get('/fiscal/dashboard', [DashboardController::class, 'fiscal'])
        ->middleware(RequierePlan::class . ':pro,despacho')
        ->name('fiscal.dashboard');
    
    // Conciliación y DIOT — solo Despacho
    Route::get('/fiscal/conciliacion', [DashboardController::class, 'conciliacion'])
        ->middleware(RequierePlan::class . ':despacho')
        ->name('fiscal.conciliacion');
    
    // Suscripción
    Route::get('/suscripcion',      [SuscripcionController::class, 'index'])->name('suscripcion.index');
    Route::post('/suscripcion/pro', [SuscripcionController::class, 'suscribirPro'])->name('suscripcion.pro');
});

// Webhooks de pagos (sin CSRF)
Route::post('/webhooks/conekta', [WebhookController::class, 'conekta'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/webhooks/stripe',  [WebhookController::class, 'stripe'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
```

```php
// routes/api.php — endpoints JSON para Alpine.js y polling

Route::middleware('auth:sanctum')->group(function () {
    // Autenticación SAT (recibe token firmado del browser)
    Route::post('/sat/autenticar',         [DescargaController::class, 'autenticarSat']);
    
    // Estado del job de descarga (polling desde el frontend)
    Route::get('/descarga/{job}/estado',   [DescargaController::class, 'estadoJson']);
    
    // Lista CFDI con filtros (para tabla con Alpine.js)
    Route::get('/cfdis',                   [CfdiController::class, 'indexJson']);
    
    // Datos del dashboard (Chart.js)
    Route::get('/fiscal/resumen',          [DashboardController::class, 'resumenJson']);
    Route::get('/fiscal/por-mes',          [DashboardController::class, 'porMesJson']);
});
```
