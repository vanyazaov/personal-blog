<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/../src/Router.php';
require __DIR__ . '/../src/functions.php';

header('Content-Type: text/html; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = require __DIR__ . '/../src/routes.php';

dispatch($routes, $method, $path);
