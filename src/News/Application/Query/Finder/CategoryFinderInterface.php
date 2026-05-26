<?php

namespace App\News\Application\Query\Finder;

use App\News\Application\Query\Handler\DTO\CategoryPreviewDTO;
use App\News\Application\Query\Handler\DTO\CategoryWithTopArticlesDTO;
use App\News\Domain\ValueObject\CategoryID;

interface CategoryFinderInterface
{
    public function findOneById(CategoryID $id): ?CategoryPreviewDTO;

    /**
     * @return CategoryPreviewDTO[]
     */
    public function findAll(): array;

    /**
     * Categories with their currently-promoted top articles; used on the home page.
     *
     * @return CategoryWithTopArticlesDTO[]
     */
    public function findCategoriesWithTopArticles(): array;
}
