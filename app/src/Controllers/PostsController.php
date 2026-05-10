<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PostsRepository;

class PostsController
{
    public function __construct(private PostsRepository $repo) {}

    public function index(string $id): void
    {
        $categoryId = (int) $id;
        $posts = $this->repo->findByCategory($categoryId, 10);
        render('posts/list', ['categoryId' => $categoryId, 'posts' => $posts]);
    }

    public function show(string $categoryId, string $id): void
    {
        $categoryId = (int) $categoryId;
        $articleId = (int) $id;
        render('posts/show', [
            'categoryId' => $categoryId,
            'articleId' => $articleId,
        ]);
    }
}
