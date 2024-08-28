<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\UpdateCategoryCommand;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use DateTime;

class UpdateCategoryHandler
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(UpdateCategoryCommand $command): void
    {
        $category = $this->categoryRepository->find($command->categoryID->getValue());
        if (!$category) {
            throw new \Exception('Category not found');
        }

        $category->setTitle($command->title);
        $category->setUpdatedAt(new DateTime());

        $this->categoryRepository->save($category);
    }
}