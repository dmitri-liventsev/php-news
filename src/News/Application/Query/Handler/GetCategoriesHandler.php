<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\Finder\CategoryFinderInterface;
use App\News\Application\Query\GetCategoriesQuery;

class GetCategoriesHandler
{
    public function __construct(private readonly CategoryFinderInterface $categoryFinder)
    {
    }

    public function __invoke(GetCategoriesQuery $query): array
    {
        return $this->categoryFinder->findAll();
    }
}
