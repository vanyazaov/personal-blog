<?php

declare(strict_types=1);

namespace App\Repositories;

final class PostsRepository {
    public function __construct(private \PDO $pdo) {}

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
        $stmt->bindValue(':category_id', $limit, \PDO::PARAM_INT);
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

}