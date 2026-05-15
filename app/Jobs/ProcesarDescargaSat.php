<?php

namespace App\Jobs;

use App\Enums\DescargaEstado;
use App\Models\DescargaJob;
use App\Services\SatWebService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcesarDescargaSat implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public readonly int $descargaJobId)
    {
    }

    public function handle(SatWebService $sat): void
    {
        $descarga = DescargaJob::query()->findOrFail($this->descargaJobId);

        try {
            $descarga->forceFill([
                'estado' => DescargaEstado::Autenticando,
                'iniciado_en' => now(),
            ])->save();

            $solicitudId = $sat->solicitarDescarga($descarga->toArray());

            $descarga->forceFill([
                'estado' => DescargaEstado::Solicitada,
                'solicitud_id' => $solicitudId,
            ])->save();
        } catch (Throwable $exception) {
            $descarga->forceFill([
                'estado' => DescargaEstado::Fallida,
                'mensaje_error' => __('app.descarga.error_generic'),
                'terminado_en' => now(),
            ])->save();

            Log::error('Fallo ProcesarDescargaSat', [
                'descarga_job_id' => $this->descargaJobId,
                'exception' => $exception,
            ]);

            throw $exception;
        }
    }
}
