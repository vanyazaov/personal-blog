<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PostsRepository;

class HomeController
{
    public function __construct(private readonly PostsRepository $postsRepository) {}

    public function index(): void
    {
        $categories = $this->postsRepository->getAllCategories();
        $categoryPost = [];
        foreach ($categories as $category) {
            $categoryPost[$category->id] = [

                'category_name' => $category->name,
                'posts' => $this->postsRepository->getRelatedPosts($category->id, 0),

            ];
        }

        render('home', ['categoryPost' => $categoryPost]);
    }
}
