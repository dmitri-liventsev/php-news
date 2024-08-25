<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\CreateCategoryCommand;
use App\News\Domain\Entity\Category;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\ValueObject\CategoryID;

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

    private function buildCategory(CreateCategoryCommand $command): Category {
        $category = new Category();
        $category->setTitle($command->title)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $category;
    }
}