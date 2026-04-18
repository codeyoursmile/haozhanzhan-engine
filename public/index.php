<?php

// ========== 一键安装检测 ==========
$lockFile = __DIR__ . '/../storage/install.lock';
// 注意：install.php 和 index.php 在同一目录（public）
if (!file_exists($lockFile) && strpos($_SERVER['REQUEST_URI'], '/install.php') === false) {
    header('Location: /install.php');
    exit;
}
// ========== 一键安装检测结束 ==========


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

$app->handleRequest(Request::capture());
