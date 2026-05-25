<?php

namespace App\News\Application\Query\Finder;

use App\News\Application\Query\Handler\DTO\ArticleDTO;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;
use DateTimeInterface;

interface ArticleFinderInterface
{
    public function findOneById(ArticleID $id): ?ArticleDTO;

    /**
     * @return ArticleDTO[]
     */
    public function findRecentPage(int $limit, int $offset): array;

    /**
     * @return ArticleDTO[]
     */
    public function findByCategoryPage(CategoryID $categoryID, int $limit, int $offset): array;

    /**
     * @return ArticleDTO[]
     */
    public function findTopSince(DateTimeInterface $since, int $limit): array;
}
