<?php

declare(strict_types=1);

namespace App\Repositories;

final class PostsRepository {
    public function __construct(private \PDO $pdo) {}

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM posts WHERE id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }    

    public function findAll(int $limit, int $offset = 0): array {
        $stmt = $this->pdo->prepare('
            SELECT * FROM posts ORDER BY n.created_at DESC
            LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function countAll(): int {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
    } 
    
    public function findByCategory(int $categoryId, int $limit, int $offset = 0): array {
        $stmt = $this->pdo->prepare('
            SELECT posts.*, post_categories.*, categories.name as category
            FROM `post_categories`
            INNER JOIN posts ON posts.id = post_id
            INNER JOIN categories ON categories.id = category_id
            WHERE `category_id` = :category_id 
            ORDER BY posts.created_at DESC
            LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue(':category_id', $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } 
    
    public function countByCategory(int $categoryId): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM posts WHERE `category_id` =  ?");
        $stmt->execute([$categoryId]);
        return (int) $stmt->fetchColumn();
    } 

    public function getRelatedPosts(int $categoryId, int $exceptId, int $limit = 3): array {
        $stmt = $this->pdo->prepare('SELECT DISTINCT p.id, p.*
                FROM posts p
                INNER JOIN post_categories pc ON pc.post_id = p.id
                WHERE pc.category_id = :category_id AND p.id != :except_id 
                ORDER BY RAND()
                LIMIT :limit');
        $stmt->bindValue(':category_id', $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(':except_id', $exceptId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllCategories(): array {
        return $this->pdo->query('SELECT * FROM categories')->fetchAll();
    }

}