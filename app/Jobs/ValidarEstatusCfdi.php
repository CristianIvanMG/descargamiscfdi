<?php

namespace App\Jobs;

use App\Models\Cfdi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ValidarEstatusCfdi implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(public readonly int $cfdiId)
    {
    }

    public function handle(): void
    {
        $cfdi = Cfdi::query()->findOrFail($this->cfdiId);

        Log::info('Validacion de estatus CFDI programada', [
            'cfdi_id' => $cfdi->getKey(),
            'uuid' => $cfdi->uuid,
        ]);
    }
}
