<?php

declare(strict_types=1);

namespace App\DTO;

class PostDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $body,
        public readonly string $picture,
        public readonly string $created_at,
    ) {}

    /**
     * @param array{
     *     id: int|string,
     *     title: string,
     *     body: string,
     *     picture: string,
     *     created_at?: string,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            title: $data['title'],
            body: $data['body'],
            picture: $data['picture'],
            created_at: ($data['created_at'] ?? ''),
        );
    }
}
