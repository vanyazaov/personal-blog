<?php

declare(strict_types=1);

namespace App;

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');

$routes = require __DIR__ . '/../src/routes.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    dispatch($routes, $method, $path);
} catch (\Throwable $e) {
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    render('errors/500');
}

