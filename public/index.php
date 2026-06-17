<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

if (DIRECTORY_SEPARATOR === '\\') {
    $app->booted(function () {
        set_error_handler(function ($errno, $errstr) {
            if ($errno === E_WARNING && str_contains($errstr, 'Header may not contain more than a single header')) {
                return true;
            }
            return false;
        });
    });
}

$app->handleRequest(Request::capture());
