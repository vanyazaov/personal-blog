<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

header('Content-Type: text/html; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

/**
 * Использование паттерна Front Controller с роутером на массиве
 */
$routes = [
    ['GET', '/', 'homePage'],
    ['GET', '/category/{id}' , 'showArticleList'],
];

dispatch($routes, $method, $path);

function dispatch(array $routes, string $method, string $path): void
{
    foreach ($routes as [$routeMethod, $routePath, $routeAction]) {
        if ($routeMethod !== $method) { continue; }

        // из $routePath делаем выражение для regex с именованной группой
        $pattern = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $routePath) . '$#';
        
        // Ищем совпадения по созданному паттерну
        if (preg_match($pattern, $path, $matches)) {
            // Передаем параметры в функцию
            if (count($matches) >= 3) {
               echo call_user_func($routeAction, $matches[1]); 
            } else {
               echo call_user_func($routeAction); 
            }
            exit();
        }
    }

    // Ничего не нашлось
    http_response_code(404);
    echo notFound();
}


function homePage(): string {
    return "<h1>Рад вас видеть на моём блоге!</h1>";
}
function showArticleList(string $category_id): string {
    $category_id = (int)$category_id;
    return "<h1>Список статей в категории №$category_id</h1>";
}
function notFound(): string {
    return "Ничего не найдено!";
}