<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Izinkan ngrok dan semua origin selama development.
    | Di production, ganti allowed_origins dengan domain spesifik.
    |
    */

    'paths' => ['api/*', 'broadcasting/auth', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // '*' = izinkan semua origin (termasuk URL ngrok yang berubah-ubah)
    // Di production, ganti dengan domain spesifik: ['https://app.jajanku.com']
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [
        // Izinkan semua subdomain ngrok secara pattern
        '#^https?://.*\.ngrok(-free)?\.app$#',
        '#^https?://.*\.ngrok\.io$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // PENTING: harus false jika allowed_origins = ['*']
    // Set ke true hanya jika pakai allowed_origins spesifik dengan credentials (cookie/session)
    'supports_credentials' => false,

];
