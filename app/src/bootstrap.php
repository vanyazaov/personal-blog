<?php

declare(strict_types=1);

namespace App;

use PDO;

require __DIR__ . '/../src/Router.php';
require __DIR__ . '/../src/helper.php';

$env = parse_ini_file(__DIR__ . "/../.env.ini");

$host = isset($env['DB_HOST']) && \is_string($env['DB_HOST'])
    ? $env['DB_HOST']
    : 'localhost';
$dbname = isset($env['DB_NAME']) && \is_string($env['DB_NAME'])
    ? $env['DB_NAME']
    : 'app';
$dbuser = isset($env['DB_USER']) && \is_string($env['DB_USER'])
    ? $env['DB_USER']
    : 'localhost';
$dbpass = isset($env['DB_PASS']) && \is_string($env['DB_PASS'])
    ? $env['DB_PASS']
    : 'app';

$dsn = \sprintf(
    'mysql:host=%s;dbname=%s;charset=utf8mb4',
    $host,
    $dbname
);

$pdo = new PDO($dsn, $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
