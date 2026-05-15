<?php

namespace App\Jobs;

use App\Models\Cfdi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EnviarAlertaCancelacion implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $cfdiId)
    {
    }

    public function handle(): void
    {
        $cfdi = Cfdi::query()->findOrFail($this->cfdiId);

        Log::warning('Alerta de cancelacion CFDI pendiente de canal de email', [
            'cfdi_id' => $cfdi->getKey(),
            'uuid' => $cfdi->uuid,
        ]);
    }
}
