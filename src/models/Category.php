<?php

declare(strict_types=1);
namespace app\models;

final class Category
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $createdAt,
        public readonly ?string $updatedAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            title: (string) $row['title'],
            description: (string) $row['description'],
            createdAt: (string) $row['created_at'],
            updatedAt: isset($row['updated_at']) ? (string) $row['updated_at'] : null,
        );
    }
}