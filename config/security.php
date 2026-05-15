<?php

return [
    'headers' => [
        'hsts_max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
        'allow_cdn' => env('SECURITY_ALLOW_CDN', true),
    ],
];
