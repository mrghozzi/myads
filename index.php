<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Auto-create .env from .env.example if missing (fresh install support)
if (!file_exists(__DIR__.'/.env') && file_exists(__DIR__.'/.env.example')) {
    copy(__DIR__.'/.env.example', __DIR__.'/.env');
    // Remove stale installed marker on fresh install
    @unlink(__DIR__.'/storage/installed');
}

// Ensure required storage directories exist (shared hosting may lack them)
foreach ([
    __DIR__.'/storage/framework/cache/data',
    __DIR__.'/storage/framework/sessions',
    __DIR__.'/storage/framework/views',
    __DIR__.'/storage/framework/testing',
    __DIR__.'/storage/logs',
    __DIR__.'/storage/app/public',
    __DIR__.'/bootstrap/cache',
] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
