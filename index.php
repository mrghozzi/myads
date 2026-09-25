<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ===========================================================================
// SECURITY: Block access to sensitive files BEFORE Laravel boots.
// This is the absolute first line of defence and runs before the framework,
// Composer autoload, or any middleware. It catches /env, /.env, /composer.json,
// /artisan, and similar paths that could leak credentials on servers where
// .htaccess rules are not supported (e.g. Nginx, LiteSpeed, misconfigured Apache).
// ===========================================================================
$requestUri = urldecode($_SERVER['REQUEST_URI'] ?? '');
// Strip query string and normalize
$requestPath = strtolower(trim(parse_url($requestUri, PHP_URL_PATH), '/'));
// Also handle subdirectory installs: extract only the last path segment(s) relative to script
$scriptDir = trim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
if ($scriptDir !== '' && str_starts_with($requestPath, strtolower($scriptDir) . '/')) {
    $requestPath = substr($requestPath, strlen($scriptDir) + 1);
}

$blockedPatterns = [
    '#^\.?env($|\.)#',           // .env, env, .env.example, .env.testing, etc.
    '#^composer\.(json|lock)$#', // Composer files
    '#^artisan$#',               // Laravel artisan CLI
    '#^phpunit\.xml$#',          // PHPUnit config
    '#^\.git(|hub|ignore|attributes|modules)(/|$)#', // Git metadata
    '#^\.editorconfig$#',        // Editor config
];

foreach ($blockedPatterns as $pattern) {
    if (preg_match($pattern, $requestPath)) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=UTF-8');
        header('X-Content-Type-Options: nosniff');
        echo 'Access Denied';
        exit(1);
    }
}
// ===========================================================================

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

// Fix for Shared Hosts dropping the Authorization Header
if (isset($_SERVER['HTTP_X_AUTHORIZATION'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['HTTP_X_AUTHORIZATION'];
}

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
