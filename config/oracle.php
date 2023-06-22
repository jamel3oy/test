<?php

return [
    'oracle' => [
        'driver'         => 'oracle',
        // 'tns'            => env('DB_TNS', ''),
        'host'           => env('ORA_HOST', ''),
        'port'           => env('ORA_PORT', '1521'),
        'database'       => env('ORA_DATABASE', ''),
        'service_name'   => env('ORA_SERVICE', ''),
        'username'       => env('ORA_USERNAME', ''),
        'password'       => env('ORA_PASSWORD', ''),
        'charset'        => env('ORA_CHARSET', 'AL32UTF8'),
        'prefix'         => env('ORA_PREFIX', ''),
        // 'prefix_schema'  => env('DB_SCHEMA_PREFIX', ''),
        // 'edition'        => env('DB_EDITION', 'ora$base'),
        // 'server_version' => env('DB_SERVER_VERSION', '11g'),
    ],

    'msumis' => [
        'driver'         => 'oracle',
        // 'tns'            => env('DB_TNS', ''),
        'host'           => '10.1.99.40',
        'port'           => env('ORA_PORT', '1521'),
        'database'       => env('ORA_DATABASE', ''),
        'service_name'   => 'MSUMIS',
        'username'       => env('ORA_USERNAME', ''),
        'password'       => env('ORA_PASSWORD', ''),
        'charset'        => env('ORA_CHARSET', 'AL32UTF8'),
        'prefix'         => env('ORA_PREFIX', ''),
        // 'prefix_schema'  => env('DB_SCHEMA_PREFIX', ''),
        // 'edition'        => env('DB_EDITION', 'ora$base'),
        // 'server_version' => env('DB_SERVER_VERSION', '11g'),
    ],
];
