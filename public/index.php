<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Emergency Cache Clear for Deployment Fix
if (file_exists(__DIR__.'/../bootstrap/cache/config.php')) {
    unlink(__DIR__.'/../bootstrap/cache/config.php');
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
