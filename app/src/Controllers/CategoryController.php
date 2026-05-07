<?php

declare(strict_types=1);

namespace App\Controllers;

class CategoryController
{
    public function index(string $id): void
    {

        $categoryId = (int) $id;
        echo \sprintf('<h1>Список статей в категории №%d</h1>', $categoryId);
    }
}
