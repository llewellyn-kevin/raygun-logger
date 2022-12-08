<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'base_uri' => env('RAYGUN_BASE_URI'),
    'api_key' => env('RAYGUN_API_KEY'),

    'level' => 'ERROR',
    'environmnets' => [
        'production',
        'staging',
    ],
    'blacklist' => [
        // Add exceptions raygun should ignore heer.
    ],
];
