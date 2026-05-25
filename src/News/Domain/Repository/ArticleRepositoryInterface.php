<?php

namespace App\News\Domain\Repository;

use App\News\Domain\Entity\Article;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;

interface ArticleRepositoryInterface
{
    public function save(Article $article): ArticleID;

    public function deleteById(ArticleID $articleID): void;

    public function findById(ArticleID $articleID): ?Article;

    /**
     * @return Article[] the latest N articles of the category sorted by createdAt DESC
     */
    public function findLatestByCategory(CategoryID $categoryID, int $limit): array;

    /**
     * @return Article[] articles of the category currently flagged isTop=true
     */
    public function findCurrentTopByCategory(CategoryID $categoryID): array;
}
