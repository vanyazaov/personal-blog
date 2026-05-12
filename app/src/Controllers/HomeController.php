<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PostsRepository;

class HomeController
{
    public function __construct(private PostsRepository $repo) {}

    public function index(): void
    {
        $categories = $this->repo->getAllCategories();
        $categoryPost = [];
        foreach($categories as $category) {
            $categoryPost[$category['id']] = [

                'category_name' => $category['name'],
                'posts' => $this->repo->getRelatedPosts($category['id'], 0)

            ];
        }

        render('home', ['categoryPost' => $categoryPost]);
    }
}
