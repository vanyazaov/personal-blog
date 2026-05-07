<?php

declare(strict_types=1);

function homePage(): string
{
    return "<h1>Рад вас видеть на моём блоге!</h1>";
}

function showArticleList(string $categoryId): string
{
    $categoryId = (int) $categoryId;
    return sprintf('<h1>Список статей в категории №%d</h1>', $categoryId);
}

function notFound(): string
{
    return "Ничего не найдено!";
}
