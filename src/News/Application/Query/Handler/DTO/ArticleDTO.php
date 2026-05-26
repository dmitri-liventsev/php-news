<?php

namespace App\News\Application\Query\Handler\DTO;

/**
 * Read-side projection of an article. Pure data — serialization is handled at
 * the HTTP boundary by ArticleDTONormalizer.
 */
final readonly class ArticleDTO
{
    /**
     * @param array{id: int, fileName: string}|null            $image
     * @param list<array{id: int, title: string}>              $categories
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $shortDescription,
        public string $content,
        public ?array $image,
        public int $numberOfViews,
        public bool $isTop,
        public array $categories,
        public string $createdAt,
        public string $updatedAt,
        public ?string $deletedAt = null,
    ) {
    }
}
