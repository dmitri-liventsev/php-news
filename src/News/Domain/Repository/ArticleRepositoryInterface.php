<?php

namespace App\News\Domain\Repository;

use App\News\Domain\Entity\Article;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;
use DateTimeInterface;

interface ArticleRepositoryInterface
{
    public function save(Article $article): ArticleID;

    public function deleteById(ArticleID $articleID): void;

    public function increaseNumberOfView(ArticleID $articleID): void;

    public function findByCategoryWithPagination(CategoryID $categoryID, int $limit, int$offset): array;
    public function findWithPagination(int $limit, int$offset): array;

    public function resetTopArticlesByCategory($categoryID): void;

    public function findTopArticlesByCategory(CategoryID $categoryID, int $limit): array;

    public function findTopArticles(DateTimeInterface $from, int $limit): array;

    public function findById(ArticleID $articleID);
}