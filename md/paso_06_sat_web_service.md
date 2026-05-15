# PASO 6 — SERVICIO SAT · SatWebService.php
## ContadorMx · xml.contadormx.net

---

## SERVICIO SAT — SatWebService.php

```php
<?php
// app/Services/SatWebService.php

namespace App\Services;

use SoapClient;
use SoapFault;
use Exception;
use Illuminate\Support\Facades\Log;

class SatWebService
{
    // URLs oficiales del Web Service SAT (PRODUCCIÓN)
    const WSDL_AUTENTICACION = 'https://cfdidescargamasivasolicitud.clouda.sat.gob.mx/Autenticacion/Autenticacion.svc?wsdl';
    const WSDL_SOLICITUD     = 'https://cfdidescargamasivasolicitud.clouda.sat.gob.mx/SolicitudDescargaService.svc?wsdl';
    const WSDL_VERIFICACION  = 'https://cfdidescargamasivasolicitud.clouda.sat.gob.mx/VerificaSolicitudDescargaService.svc?wsdl';
    const WSDL_DESCARGA      = 'https://cfdidescargamasiva.clouda.sat.gob.mx/DescargaMasivaService.svc?wsdl';

    private string $tokenSat;
    private int $timeoutSegundos = 30;

    public function __construct(string $tokenSat)
    {
        $this->tokenSat = $tokenSat;
    }

    /**
     * Paso 1: Valida el token firmado recibido del browser y obtiene token SAT
     * IMPORTANTE: Este método recibe el XML ya firmado — nunca la clave privada
     */
    public static function autenticarConTokenFirmado(string $xmlFirmado): string
    {
        try {
            $client = new SoapClient(self::WSDL_AUTENTICACION, [
                'trace'              => false,
                'exceptions'         => true,
                'connection_timeout' => 30,
                'stream_context'     => stream_context_create([
                    'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
                ]),
            ]);

            $resultado = $client->Autentica($xmlFirmado);
            
            if (empty($resultado->AutenticaResult)) {
                throw new Exception('El SAT no devolvió token de autenticación');
            }

            return $resultado->AutenticaResult; // Token SAT (válido ~5 min para solicitar)

        } catch (SoapFault $e) {
            Log::error('SAT SOAP autenticacion error', ['fault' => $e->getMessage()]);
            throw new Exception('Error de autenticación con el SAT: ' . $e->getMessage());
        }
    }

    /**
     * Paso 2: Solicita descarga al SAT — devuelve ID de solicitud
     */
    public function solicitarDescarga(
        string $rfc,
        string $fechaInicio,   // YYYY-MM-DDTHH:MM:SS
        string $fechaFin,      // YYYY-MM-DDTHH:MM:SS
        string $rfcEmisor = '',
        string $rfcReceptor = '',
        string $tipoSolicitud = 'CFDI',  // CFDI o Metadata
        string $tipoComprobante = ''     // I,E,T,N,P o vacío para todos
    ): string {
        $client = $this->buildClient(self::WSDL_SOLICITUD);
        
        $params = [
            'solicitud' => [
                'FechaInicial'    => $fechaInicio,
                'FechaFinal'      => $fechaFin,
                'TipoSolicitud'   => $tipoSolicitud,
                'RfcReceptor'     => $rfcReceptor ?: $rfc,
                'RfcEmisor'       => $rfcEmisor,
                'RfcSolicitante'  => $rfc,
                'TipoComprobante' => $tipoComprobante,
            ],
        ];

        $resultado = $client->SolicitaDescarga($params);
        $respuesta = $resultado->SolicitaDescargaResult;

        if ($respuesta->CodEstatus !== '5000') {
            throw new Exception(
                "SAT rechazó solicitud. Código: {$respuesta->CodEstatus}. Mensaje: {$respuesta->Mensaje}"
            );
        }

        return $respuesta->IdSolicitud;
    }

    /**
     * Paso 3: Verifica si la descarga está lista — devuelve estado y paquetes
     */
    public function verificarDescarga(string $idSolicitud, string $rfc): array
    {
        $client = $this->buildClient(self::WSDL_VERIFICACION);

        $resultado = $client->VerificaSolicitudDescarga([
            'solicitud' => [
                'IdSolicitud'    => $idSolicitud,
                'RfcSolicitante' => $rfc,
            ],
        ]);

        $respuesta = $resultado->VerificaSolicitudDescargaResult;

        // Estados posibles del SAT:
        // 1=Aceptada, 2=En proceso, 3=Terminada, 4=Error, 5=Rechazada, 6=Vencida
        return [
            'estado'      => (int) $respuesta->EstadoSolicitud,
            'codigo'      => $respuesta->CodEstatus,
            'mensaje'     => $respuesta->Mensaje,
            'paquetes'    => $respuesta->IdsPaquetes ?? [],
            'total_cfdi'  => (int) ($respuesta->NumeroCFDIs ?? 0),
        ];
    }

    /**
     * Paso 4: Descarga un paquete ZIP — devuelve contenido base64
     */
    public function descargarPaquete(string $idPaquete, string $rfc): string
    {
        $client = $this->buildClient(self::WSDL_DESCARGA);

        $resultado = $client->PeticionDescargaMasivaTercerosBRequest([
            'peticionDescarga' => [
                'IdPaquete'      => $idPaquete,
                'RfcSolicitante' => $rfc,
            ],
        ]);

        $respuesta = $resultado->PeticionDescargaMasivaTercerosBRequestResult;

        if ($respuesta->CodEstatus !== '5000') {
            throw new Exception("Error descargando paquete {$idPaquete}: {$respuesta->Mensaje}");
        }

        return $respuesta->Paquete; // base64 del ZIP
    }

    private function buildClient(string $wsdl): SoapClient
    {
        return new SoapClient($wsdl, [
            'trace'              => false,
            'exceptions'         => true,
            'connection_timeout' => $this->timeoutSegundos,
            'stream_context'     => stream_context_create([
                'ssl' => ['verify_peer' => true],
                'http' => [
                    'header' => "Authorization: Bearer {$this->tokenSat}",
                ],
            ]),
        ]);
    }
}
```
