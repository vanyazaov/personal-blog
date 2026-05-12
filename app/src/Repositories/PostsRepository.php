<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\CategoryDTO;
use App\DTO\PostDTO;
use PDO;
use RuntimeException;

final readonly class PostsRepository
{
    public function __construct(private PDO $pdo) {}


    public function findById(int $id): ?PostDTO
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM posts WHERE id = ?'
        );
        $stmt->execute([$id]);

        /** @var array{id: int|string, title: string, body: string, picture: string, created_at: string}|false $result*/
        $result = $stmt->fetch();

        if ($result === false) {
            return null;
        }

        return PostDTO::fromArray($result);
    }

    /**
     * @return list<PostDTO>
     */
    public function findAll(int $limit, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM posts ORDER BY n.created_at DESC
            LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll();

        $posts = [];
        /** @var array{id: int|string, title: string, body: string, picture: string, created_at: string} $post*/
        foreach ($result as $post) {
            $posts[] = PostDTO::fromArray($post);
        }

        return $posts;
    }

    public function countAll(): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM posts');

        if ($stmt === false) {
            throw new RuntimeException('Failed to prepare statement');
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return list<PostDTO>
     */
    public function findByCategory(int $categoryId, int $limit, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('
            SELECT posts.*, post_categories.*, categories.name as category
            FROM `post_categories`
            INNER JOIN posts ON posts.id = post_id
            INNER JOIN categories ON categories.id = category_id
            WHERE `category_id` = :category_id
            ORDER BY posts.created_at DESC
            LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll();

        $posts = [];
        /** @var array{id: int|string, title: string, body: string, picture: string, created_at: string} $post*/
        foreach ($result as $post) {
            $posts[] = PostDTO::fromArray($post);
        }

        return $posts;
    }

    public function countByCategory(int $categoryId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM posts WHERE `category_id` =  ?");
        $stmt->execute([$categoryId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * @return list<PostDTO>
     */
    public function getRelatedPosts(int $categoryId, int $exceptId, int $limit = 3): array
    {
        $stmt = $this->pdo->prepare('SELECT DISTINCT p.id, p.*
                FROM posts p
                INNER JOIN post_categories pc ON pc.post_id = p.id
                WHERE pc.category_id = :category_id AND p.id != :except_id
                ORDER BY RAND()
                LIMIT :limit');
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':except_id', $exceptId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll();

        $posts = [];
        /** @var array{id: int|string, title: string, body: string, picture: string, created_at: string} $post*/
        foreach ($result as $post) {
            $posts[] = PostDTO::fromArray($post);
        }

        return $posts;
    }

    /**
     * @return list<CategoryDTO>
     */
    public function getAllCategories(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories');

        if ($stmt === false) {
            throw new RuntimeException('Failed to prepare statement');
        }

        $stmt->execute();

        $result = $stmt->fetchAll();
        $categories = [];
        /** @var array{id: int|string, name: string} $category */
        foreach ($result as $category) {
            $categories[] = CategoryDTO::fromArray($category);
        }

        return $categories;
    }
}
