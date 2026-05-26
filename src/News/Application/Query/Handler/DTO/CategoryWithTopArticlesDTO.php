<?php

namespace App\News\Application\Query\Handler\DTO;

/**
 * Read-side projection used by the public home page (/api/top-news):
 * a category with the small list of its currently-promoted top articles.
 *
 * Pure data — serialization is handled at the HTTP boundary by a dedicated
 * normalizer (CategoryWithTopArticlesDTONormalizer).
 */
final readonly class CategoryWithTopArticlesDTO
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
}
