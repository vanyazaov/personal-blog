<?php

declare(strict_types=1);

namespace App;

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/../src/Router.php';

header('Content-Type: text/html; charset=utf-8');

$routes = require __DIR__ . '/../src/routes.php';
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


dispatch($routes, $method, $path);
