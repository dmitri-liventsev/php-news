<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetTopNewsQuery;
use App\News\Domain\Repository\CategoryRepositoryInterface;

class GetTopNewsHandler
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(GetTopNewsQuery $query): array
    {
        return $this->categoryRepository->findCategoriesWithTopArticles();
    }
}