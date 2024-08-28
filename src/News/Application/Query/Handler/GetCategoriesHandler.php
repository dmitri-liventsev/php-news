<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetCategoriesQuery;
use App\News\Application\Query\Handler\DTO\CategoryPreviewDTO;
use App\News\Domain\Entity\Category;
use App\News\Domain\Repository\CategoryRepositoryInterface;

class GetCategoriesHandler
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(GetCategoriesQuery $query): array
    {
        $categories = $this->categoryRepository->findAll();

        return array_map(function (Category $category) {
            return new CategoryPreviewDTO($category);
        }, $categories);
    }
}