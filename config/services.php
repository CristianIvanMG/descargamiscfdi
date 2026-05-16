<?php

return [
    'sat' => [
        'auth_endpoint' => env('SAT_AUTH_ENDPOINT', 'https://cfdidescargamasivasolicitud.clouda.sat.gob.mx/Autenticacion/Autenticacion.svc'),
        'request_endpoint' => env('SAT_REQUEST_ENDPOINT', 'https://retendescargamasivasolicitud.clouda.sat.gob.mx/SolicitaDescargaService.svc'),
        'verify_endpoint' => env('SAT_VERIFY_ENDPOINT', 'https://retendescargamasivasolicitud.clouda.sat.gob.mx/VerificaSolicitudDescargaService.svc'),
        'download_endpoint' => env('SAT_DOWNLOAD_ENDPOINT', 'https://retendescargamasiva.clouda.sat.gob.mx/DescargaMasivaService.svc'),
    ],
    'mercadopago' => [
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
        'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),
        'checkout_url' => env('MERCADOPAGO_DONATION_URL'),
    ],
];
