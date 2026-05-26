<?php

namespace App\News\Application\Query\Handler\DTO;

use JsonSerializable;

/**
 * Read-side projection of an article. Constructed by ArticleFinder from a flat
 * DBAL row — it does NOT know about the Article aggregate.
 */
final class ArticleDTO implements JsonSerializable
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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'shortDescription' => $this->shortDescription,
            'content' => $this->content,
            'image' => $this->image,
            'numberOfViews' => $this->numberOfViews,
            'isTop' => $this->isTop,
            'categories' => $this->categories,
        ];
    }
}
