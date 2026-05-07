<?php

declare(strict_types=1);

use RuntimeException;

/**
 * TODO: $routes должен быть итерратором
 * TODO: CyclomaticComplexity 10 из 10. Уменьшить
 * TODO: ExitExpression (строка 51). Заменить на возврат/исключение.
 *
 * @param array<string, mixed> $routes
 */
function dispatch(array $routes, string $method, string $path): void
{
    foreach ($routes as [$routeMethod, $routePath, $routeAction]) {
        $routePath = is_string($routePath ?? null) ? $routePath : '';
        $nameRouteAction = is_string($routeAction ?? null) ? $routeAction : '';
        if ($routeMethod !== $method) {
            continue;
        }

        // из $routePath делаем выражение для regex с именованной группой
        $replaced = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $routePath);

        if (!is_string($replaced)) {
            throw new RuntimeException(
                sprintf('Функция preg_replace вернула не текстовое значение: %s', $routePath)
            );
        }

        $pattern = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $routePath) . '$#';

        // Ищем совпадения по созданному паттерну
        if ((bool) preg_match($pattern, $path, $matches)) {
            if (!is_callable($routeAction)) {
                continue;
            }

            // Передаем параметры в функцию
            $result = count($matches) >= 3 ? call_user_func($routeAction, $matches[1]) : call_user_func($routeAction);

            if (!is_string($result)) {
                throw new RuntimeException(
                    sprintf('Функция вернула не текстовое значение: %s', $nameRouteAction)
                );
            }

            echo $result;

            exit();
        }
    }

    // Ничего не нашлось
    http_response_code(404);
    echo notFound();
}
