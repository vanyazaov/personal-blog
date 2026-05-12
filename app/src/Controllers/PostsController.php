<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PostsRepository;

class PostsController
{
    public function __construct(private readonly PostsRepository $postsRepository) {}

    public function index(string $id): void
    {
        $categoryId = (int) $id;
        $posts = $this->postsRepository->findByCategory($categoryId, 9);
        if ($posts === []) {
            render('errors/404');
            return;
        }

        render('posts/list', ['categoryId' => $categoryId, 'posts' => $posts]);
    }

    public function show(string $categoryId, string $id): void
    {
        $categoryId = (int) $categoryId;
        $postId = (int) $id;
        $post = $this->postsRepository->findById($postId);
        $relatedPosts = $this->postsRepository->getRelatedPosts($categoryId, $postId);
        render('posts/show', [
            'categoryId' => $categoryId,
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
