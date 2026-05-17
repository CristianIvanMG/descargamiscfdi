<?php

use App\Http\Controllers\Api\DescargaEstadoController;
use App\Http\Controllers\Api\DashboardMetricasController;
use App\Http\Controllers\Api\FiscalDeclarationController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard/metricas', DashboardMetricasController::class)->name('api.dashboard.metricas');
Route::get('/descargas/{descargaJob}/estado', DescargaEstadoController::class)->name('api.descargas.estado');
Route::post('/declaraciones/mensual', FiscalDeclarationController::class)
    ->middleware('throttle:10,1')
    ->name('api.declaraciones.mensual');
