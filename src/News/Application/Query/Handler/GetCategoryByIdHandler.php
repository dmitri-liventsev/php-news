<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\Finder\CategoryFinderInterface;
use App\News\Application\Query\GetCategoryByIdQuery;
use App\News\Application\Query\Handler\DTO\CategoryPreviewDTO;
use App\News\Domain\Exception\CategoryNotFoundException;

class GetCategoryByIdHandler
{
    public function __construct(private readonly CategoryFinderInterface $categoryFinder)
    {
    }

    public function __invoke(GetCategoryByIdQuery $query): CategoryPreviewDTO
    {
        $category = $this->categoryFinder->findOneById($query->categoryID);
        if ($category === null) {
            throw CategoryNotFoundException::byId($query->categoryID);
        }

        return $category;
    }
}
