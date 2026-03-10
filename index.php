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
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
