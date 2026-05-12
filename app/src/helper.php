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

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8');
}

function fmt_date(?string $datetime, string $format = 'd.m.Y H:i'): string
{
    if ($datetime === null || $datetime === '' || $datetime === '0') {
        return '—';
    }

    $ts = strtotime($datetime);
    return $ts === false ? '—' : date($format, $ts);
}

function truncate(string $s, int $max = 200): string
{
    if (mb_strlen($s) <= $max) {
        return $s;
    }

    return mb_substr($s, 0, $max) . '…';
}
