<?php

return [
    'actions' => [
        'hide' => 'Hide',
        'show' => 'Show',
    ],
    'chart' => [
        'laravel' => 'Laravel',
        'mysql' => 'MySQL',
        'queue' => 'Queue',
        'storage' => 'Storage',
    ],
    'home' => [
        'title' => 'ContadorMx - Laravel Verification',
        'phase' => 'Step 1 - Role, context and stack',
        'heading' => 'ContadorMx is ready to validate Laravel on Hostinger',
        'subtitle' => 'Initial SaaS web base for browser-based CFDI downloads, analysis and reporting.',
        'status_ready' => 'Base ready',
        'checklist_title' => 'First upload validation',
        'chart_title' => 'Active stack',
        'chart_label' => 'Visual distribution of the initial stack',
        'checklist' => [
            [
                'title' => 'Laravel 11 boot',
                'body' => 'The application uses the modern Laravel 11 bootstrap and production-ready web routing.',
            ],
            [
                'title' => 'Hostinger Business',
                'body' => 'Configuration is prepared for PHP 8.2, MySQL 8, cron jobs and SSH.',
            ],
            [
                'title' => 'Defined drivers',
                'body' => 'Sessions and queues use the database; cache uses files; private storage stays outside public.',
            ],
            [
                'title' => 'Base frontend',
                'body' => 'Bootstrap 5, Alpine.js and Chart.js load from CDN for this initial validation.',
            ],
        ],
    ],
    'stack' => [
        'backend' => 'Backend',
        'database' => 'Database',
        'queue' => 'Async queue',
        'queue_value' => 'Database driver',
        'storage' => 'Files',
        'storage_value' => 'Private local storage',
    ],
];
