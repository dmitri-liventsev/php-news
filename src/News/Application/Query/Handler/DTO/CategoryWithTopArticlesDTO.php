<?php

namespace App\News\Application\Query\Handler\DTO;

use JsonSerializable;

/**
 * Read-side projection used by the public home page (/api/top-news):
 * a category with the small list of its currently-promoted top articles.
 */
final class CategoryWithTopArticlesDTO implements JsonSerializable
{
    /**
     * @param ArticleDTO[] $articles
     */
    public function __construct(
        public int $id,
        public string $title,
        public array $articles,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'articles' => $this->articles,
        ];
    }
}
