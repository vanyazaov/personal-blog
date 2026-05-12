<?php

declare(strict_types=1);

namespace App\DTO;

class CategoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}

    /**
     * @param array{
     *     id: int|string,
     *     name: string,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['name'],
        );
    }
}
