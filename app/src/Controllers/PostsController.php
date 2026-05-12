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
        $posts = $this->repo->findByCategory($categoryId, 9);
        if (empty($posts)) {
            render('errors/404');
            return;
        }
        render('posts/list', ['categoryId' => $categoryId, 'posts' => $posts]);
    }

    public function show(string $categoryId, string $id): void
    {
        $categoryId = (int) $categoryId;
        $postId = (int) $id;
        $post = $this->repo->findById($postId);
        $relatedPosts = $this->repo->getRelatedPosts($categoryId, $postId);
        render('posts/show', [
            'categoryId' => $categoryId,
            'post' => $post,
            'relatedPosts' => $relatedPosts
        ]);
    }
}
