<?php

return [
    'actions' => [
        'hide' => 'Ocultar',
        'show' => 'Mostrar',
    ],
    'chart' => [
        'laravel' => 'Laravel',
        'mysql' => 'MySQL',
        'queue' => 'Cola',
        'storage' => 'Storage',
    ],
    'home' => [
        'title' => 'ContadorMx - Verificacion Laravel',
        'phase' => 'Paso 1 - Rol, contexto y stack',
        'heading' => 'ContadorMx esta listo para validar Laravel en Hostinger',
        'subtitle' => 'Base inicial del SaaS web para descarga masiva, analisis y reportes CFDI desde navegador.',
        'status_ready' => 'Base lista',
        'checklist_title' => 'Validacion de primera subida',
        'chart_title' => 'Stack activo',
        'chart_label' => 'Distribucion visual del stack inicial',
        'checklist' => [
            [
                'title' => 'Arranque Laravel 11',
                'body' => 'La aplicacion usa el bootstrap moderno de Laravel 11 y rutas web listas para produccion.',
            ],
            [
                'title' => 'Hostinger Business',
                'body' => 'Configuracion preparada para PHP 8.2, MySQL 8, cron jobs y SSH.',
            ],
            [
                'title' => 'Drivers definidos',
                'body' => 'Sesiones y cola usan base de datos; cache usa archivos; storage privado queda fuera de public.',
            ],
            [
                'title' => 'Frontend base',
                'body' => 'Bootstrap 5, Alpine.js y Chart.js cargan desde CDN para esta validacion inicial.',
            ],
        ],
    ],
    'stack' => [
        'backend' => 'Backend',
        'database' => 'Base de datos',
        'queue' => 'Cola async',
        'queue_value' => 'Database driver',
        'storage' => 'Archivos',
        'storage_value' => 'Storage privado local',
    ],
];
