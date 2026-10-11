<?php

namespace app\models;

final class Article
{
    /**
     * @param list<int> $categoryIds
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly ?string $text,
        public readonly ?string $image,
        public readonly int $views,
        public readonly string $createdAt,
        public readonly ?string $updatedAt = null,
        public readonly array $categoryIds = [],
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
            text: isset($row['text']) ? (string) $row['text'] : null,
            image: isset($row['image']) ? (string) $row['image'] : null,
            views: (int) $row['views'],
            createdAt: (string) $row['created_at'],
            updatedAt: isset($row['updated_at']) ? (string) $row['updated_at'] : null,
            categoryIds: [],
        );
    }
}