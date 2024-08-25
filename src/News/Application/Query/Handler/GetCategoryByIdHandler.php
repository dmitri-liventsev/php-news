<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetCategoryByIdQuery;
use App\News\Domain\Entity\Category;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetCategoryByIdHandler
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(GetCategoryByIdQuery $query): Category
    {
        $category = $this->categoryRepository->find($query->categoryID);

        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        return $category;
    }
}