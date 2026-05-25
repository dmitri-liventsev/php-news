<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\CreateCategoryCommand;
use App\News\Domain\Entity\Category;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\ValueObject\CategoryID;
use App\News\Domain\ValueObject\CategoryTitle;

class CreateCategoryHandler
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(CreateCategoryCommand $command): CategoryID
    {
        $category = $this->buildCategory($command);
        return $this->categoryRepository->save($category);
    }

    private function buildCategory(CreateCategoryCommand $command): Category
    {
        return Category::create(new CategoryTitle($command->title));
    }
}