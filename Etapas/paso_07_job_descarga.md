# PASO 7 — JOB ASYNC · ProcesarDescargaSat.php
## ContadorMx · xml.contadormx.net

---

## JOB ASYNC — ProcesarDescargaSat.php

```php
<?php
// app/Jobs/ProcesarDescargaSat.php

namespace App\Jobs;

use App\Models\DescargaJob;
use App\Models\Rfc;
use App\Services\SatWebService;
use App\Services\CfdiParser;
use App\Services\CfdiStorage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class ProcesarDescargaSat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;   // 5 minutos máximo por job
    public int $tries   = 3;     // reintentos si falla
    public int $backoff = 60;    // espera 60s entre reintentos

    public function __construct(
        private int    $descargaJobId,
        private string $tokenSat,        // token de sesión SAT (efímero)
        private string $rfc,
        private string $fechaInicio,
        private string $fechaFin,
        private string $direccion        // 'emitido','recibido','ambos'
    ) {}

    public function handle(CfdiParser $parser, CfdiStorage $storage): void
    {
        $job = DescargaJob::findOrFail($this->descargaJobId);
        $job->update(['estado' => 'procesando', 'iniciado_en' => now()]);

        try {
            $sat = new SatWebService($this->tokenSat);
            
            $direcciones = $this->direccion === 'ambos'
                ? ['emitido', 'recibido']
                : [$this->direccion];

            $totalNuevos = 0;

            foreach ($direcciones as $dir) {
                $rfcEmisor   = $dir === 'emitido'  ? $this->rfc : '';
                $rfcReceptor = $dir === 'recibido' ? $this->rfc : '';

                // Paso 1: Solicitar
                $idSolicitud = $sat->solicitarDescarga(
                    $this->rfc,
                    $this->fechaInicio . 'T00:00:00',
                    $this->fechaFin    . 'T23:59:59',
                    $rfcEmisor,
                    $rfcReceptor
                );

                // Paso 2: Esperar y verificar (polling con backoff)
                $paquetes = $this->esperarPaquetes($sat, $idSolicitud);

                // Paso 3: Descargar y procesar cada paquete
                foreach ($paquetes as $idPaquete) {
                    $zipBase64 = $sat->descargarPaquete($idPaquete, $this->rfc);
                    $nuevos    = $this->procesarZip($zipBase64, $job, $parser, $storage, $dir);
                    $totalNuevos += $nuevos;

                    // Actualizar progreso en tiempo real
                    $job->increment('total_descargado');
                }
            }

            $job->update([
                'estado'       => 'completado',
                'total_nuevos' => $totalNuevos,
                'completado_en'=> now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Descarga SAT falló', [
                'job_id' => $this->descargaJobId,
                'error'  => $e->getMessage(),
            ]);
            $job->update([
                'estado'        => 'error',
                'mensaje_error' => $e->getMessage(),
            ]);
            throw $e; // Laravel reintentará según $tries
        }
    }

    private function esperarPaquetes(SatWebService $sat, string $idSolicitud): array
    {
        $maxIntentos = 20;
        $espera      = 15; // segundos entre verificaciones

        for ($i = 0; $i < $maxIntentos; $i++) {
            sleep($espera);
            $verificacion = $sat->verificarDescarga($idSolicitud, $this->rfc);

            match ($verificacion['estado']) {
                3       => (fn() => true)(), // Terminada — continúa
                1, 2    => null,              // Aceptada / En proceso — seguir esperando
                default => throw new \Exception(
                    "SAT rechazó/anuló solicitud. Estado: {$verificacion['estado']}. {$verificacion['mensaje']}"
                ),
            };

            if ($verificacion['estado'] === 3) {
                return (array) $verificacion['paquetes'];
            }
        }

        throw new \Exception('El SAT no procesó la solicitud en el tiempo esperado');
    }

    private function procesarZip(
        string $zipBase64,
        DescargaJob $job,
        CfdiParser $parser,
        CfdiStorage $storage,
        string $direccion
    ): int {
        $zipContent  = base64_decode($zipBase64);
        $tmpZip      = tempnam(sys_get_temp_dir(), 'cfdi_');
        file_put_contents($tmpZip, $zipContent);

        $zip    = new ZipArchive();
        $nuevos = 0;

        if ($zip->open($tmpZip) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $xmlContent = $zip->getFromIndex($i);
                $filename   = $zip->getNameIndex($i);

                if (!str_ends_with($filename, '.xml')) continue;

                try {
                    $datos = $parser->parsear($xmlContent, $direccion, $job->rfc_id, $job->user_id);
                    
                    // Evitar duplicados por UUID
                    $existe = \App\Models\Cfdi::where('uuid', $datos['uuid'])->exists();
                    if (!$existe) {
                        $cfdi = \App\Models\Cfdi::create($datos);
                        
                        // Guardar XML cifrado en disco (solo plan Pro/Despacho)
                        $user = $job->user;
                        if (in_array($user->plan, ['pro', 'despacho'])) {
                            $ruta = $storage->guardarXml($xmlContent, $job->user_id, $job->rfc->rfc, $datos['uuid']);
                            $cfdi->update(['ruta_xml' => $ruta]);
                        }
                        
                        $nuevos++;
                    }
                } catch (\Exception $e) {
                    Log::warning("CFDI no procesado: {$filename}", ['error' => $e->getMessage()]);
                }
            }
            $zip->close();
        }

        unlink($tmpZip);
        return $nuevos;
    }
}
```
