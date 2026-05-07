<?php

declare(strict_types=1);

namespace App\Controllers;

class ArticleController
{
    public function show(string $categoryId, string $id): void
    {
        $categoryId = (int) $categoryId;
        $articleId = (int) $id;
        echo \sprintf('<h1>Статья %d из категории %d</h1>', $articleId, $categoryId);
    }
}
