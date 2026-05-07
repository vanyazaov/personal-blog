<?php

declare(strict_types=1);

/**
 * TODO: $data должен быть итерратором
 *
 * @param array<string, mixed> $data
 */
function render(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);

    ob_start();
    include __DIR__ . '/../templates/pages/' . $template . '.php';
    $content = ob_get_clean();

    include __DIR__ . '/../templates/layouts/main.php';
}
