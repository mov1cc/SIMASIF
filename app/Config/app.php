<?php

declare(strict_types=1);

return [
    'name'     => env('APP_NAME', 'SIMASIF'),
    'env'      => env('APP_ENV', 'production'),
    'debug'    => (bool) env('APP_DEBUG', false),
    'url'      => env('APP_URL', 'http://localhost:8000'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
    'session'  => [
        'name'     => 'simasif_session',
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
    ],
];