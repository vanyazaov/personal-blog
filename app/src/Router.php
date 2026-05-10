<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

/**
 * TODO: $routes должен быть итерратором
 * TODO: CyclomaticComplexity 10 из 10. Уменьшить
 * TODO: ExitExpression (строка 51). Заменить на возврат/исключение.
 *
 * @param array<string, mixed> $routes
 */
function dispatch(array $routes, string $method, string $path, \PDO $pdo): void
{
    foreach ($routes as [$routeMethod, $routePath, $routeAction]) {
        $routePath = \is_string($routePath ?? null) ? $routePath : '';
        $nameRouteAction = \is_string($routeAction ?? null) ? $routeAction : '';
        if ($routeMethod !== $method) {
            continue;
        }

        // из $routePath делаем выражение для regex с именованной группой
        $replaced = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $routePath);

        if (!\is_string($replaced)) {
            throw new RuntimeException(
                \sprintf('Функция preg_replace вернула не текстовое значение: %s', $routePath)
            );
        }

        $pattern = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $routePath) . '$#';

        // Ищем совпадения по созданному паттерну
        if ((bool) preg_match($pattern, $path, $matches)) {
            // Оставим только именнованные параметры
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            // Распарсим строку в массив по @
            [$controllerName, $controllerAction] = explode('@', $nameRouteAction);

            
            // временное решение
            $repositories = [
                'App\Controllers\PostsController' => 'App\Repositories\PostsRepository'
            ];
            $repo = null;
            if (isset($repositories[$controllerName])) {
               $repo = new $repositories[$controllerName]($pdo);
            }

            $controller = new $controllerName($repo);
            $callable = [$controller, $controllerAction];
            if (!\is_callable($callable)) {
                continue;
            }

            \call_user_func_array($callable, array_values($params));
            return;
        }
    }

    // Ничего не нашлось
    http_response_code(404);
    render('errors/404');
}
