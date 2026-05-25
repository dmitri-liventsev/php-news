<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\UpdateCategoryCommand;
use App\News\Domain\Exception\CategoryNotFoundException;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\ValueObject\CategoryTitle;

class UpdateCategoryHandler
{
    public function __construct(private readonly CategoryRepositoryInterface $categoryRepository)
    {
    }

    public function __invoke(UpdateCategoryCommand $command): void
    {
        $category = $this->categoryRepository->findById($command->categoryID);
        if (!$category) {
            throw CategoryNotFoundException::byId($command->categoryID);
        }

        $category->rename(new CategoryTitle($command->title));

        $this->categoryRepository->save($category);
    }
}
