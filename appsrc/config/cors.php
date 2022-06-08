<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => [
        'GET', 'POST', 'PATCH', 'DELETE', 'OPTIONS', 'patch'
    ],

    'allowed_origins' => [
        '*'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Access-Control-Allow-Origin',
        'Access-Control-Allow-Headers',
        'X-Requested-With',
        'Content-Type',
        'X-Token-Auth',
        'Authorization',
        'Allow-Origin',
        '*'
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
