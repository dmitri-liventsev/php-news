<?php

namespace App\News\Application\Query\Finder;

use App\News\Application\Query\Handler\DTO\CategoryPreviewDTO;
use App\News\Domain\ValueObject\CategoryID;

interface CategoryFinderInterface
{
    public function findOneById(CategoryID $id): ?CategoryPreviewDTO;

    /**
     * @return CategoryPreviewDTO[]
     */
    public function findAll(): array;

    /**
     * Flat projection of categories with their top articles; used on the home page.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findCategoriesWithTopArticles(): array;
}
