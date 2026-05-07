<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

header('Content-Type: text/html; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

/**
 * Использование паттерна Front Controller с динамическим параметром на основе регулярных выражений
 */
if ($method === 'GET' && $path === '/') {
    echo homePage();
}
elseif ($method === 'GET' && preg_match('#^/category/(\d+)$#', $path, $m)) {
    echo showArticleList((int) $m[1]);
} else {
    echo notFound();
}

function homePage(): string {
    return "<h1>Рад вас видеть на моём блоге!</h1>";
}
function showArticleList(int $category_id): string {
    return "<h1>Список статей в категории №$category_id</h1>";
}
function notFound(): string {
    return "Ничего не найдено!";
}