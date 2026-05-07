<?php

declare(strict_types=1);

namespace App\Controllers;

class ArticlesController
{
    public function index(string $id): void
    {

        $categoryId = (int) $id;
        render('articles/list', ['categoryId' => $categoryId]);
    }

    public function show(string $categoryId, string $id): void
    {
        $categoryId = (int) $categoryId;
        $articleId = (int) $id;
        render('articles/show', [
            'categoryId' => $categoryId,
            'articleId' => $articleId,
        ]);
    }
}
