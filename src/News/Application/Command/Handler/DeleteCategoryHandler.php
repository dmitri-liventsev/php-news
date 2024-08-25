<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\DeleteCategoryCommand;
use App\News\Domain\Repository\CategoryRepositoryInterface;

class DeleteCategoryHandler
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $articleRepository)
    {
        $this->categoryRepository = $articleRepository;
    }

    public function __invoke(DeleteCategoryCommand $command): void
    {
        $this->categoryRepository->deleteById($command->categoryID);
    }
}