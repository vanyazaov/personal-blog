<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

header('Content-Type: text/html; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

/**
 * Использование паттерна Front Controller в элементарном виде, на основе конструкции выбора match
 */
match (true) {
    $method === 'GET' && $path === '/'              => homePage(),
    $method === 'GET' && $path === '/category/1'          => showArticleList(1),
    $method === 'GET' && $path === '/category/2'          => showArticleList(2),
    $method === 'GET' && $path === '/category/3'          => showArticleList(3),
    default                                          => notFound(),
};

function homePage(): void {
    echo "<h1>Рад вас видеть на моём блоге!</h1>";
}
function showArticleList(int $category_id): void {
    echo "<h1>Список статей в категории №$category_id</h1>";
}
function notFound(): void {
    echo "Ничего не найдено!";
}