<?php

namespace App\News\Domain\Repository;

use App\News\Domain\Entity\Category;
use App\News\Domain\ValueObject\CategoryID;

interface CategoryRepositoryInterface
{
    public function findById(CategoryID $categoryID): ?Category;

    public function findByIds(array $ids): array;

    public function save(Category $category): CategoryID;

    public function deleteById(CategoryID $categoryID): void;

    public function findCategoriesWithTopArticles(): array;
}