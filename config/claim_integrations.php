<?php

return [
    'simulator_enabled' => env('CLAIM_SIMULATOR_ENABLED', false),
    'simulator_origin' => env('CLAIM_SIMULATOR_ORIGIN', 'http://127.0.0.1:8001'),
    'reasons' => ['Todo riesgo', 'ampliacion', 'actualizaciondevalores', 'cotizarsincerrar', 'robo', 'incendio', 'posibleDT'],
    'max_batch' => 25,
    'runtime' => [
        'php_binary' => env('CLAIM_SIMULATION_PHP_BINARY', PHP_BINDIR.'/php'),
        'directory' => storage_path('app/simulation-runtime'),
    ],
    'engine' => [
        'cycle_limit' => 20,
        'http_timeout' => env('CLAIM_SIMULATION_HTTP_TIMEOUT', 8),
        'max_attempts' => env('CLAIM_SIMULATION_MAX_ATTEMPTS', 4),
        'backoff' => [10, 30, 60, 120],
        'faults_enabled' => env('CLAIM_SIMULATION_FAULTS_ENABLED', false),
    ],
];
