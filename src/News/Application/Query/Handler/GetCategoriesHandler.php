<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetCategoriesQuery;
use App\News\Application\Query\Handler\DTO\CategoryPreviewDTO;
use App\News\Domain\Entity\Category;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use Doctrine\Common\Collections\Collection;

class GetCategoriesHandler
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param GetCategoriesQuery $query
     * @return Collection|Category[]
     */
    public function __invoke(GetCategoriesQuery $query): array
    {
        $categories = $this->categoryRepository->findAll();
        $categories = array_map(function (Category $category) {
            return new CategoryPreviewDTO($category);
        }, $categories);

        return $categories;
    }
}