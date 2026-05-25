<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\Finder\CategoryFinderInterface;
use App\News\Application\Query\GetTopNewsQuery;

class GetTopNewsHandler
{
    public function __construct(private readonly CategoryFinderInterface $categoryFinder)
    {
    }

    public function __invoke(GetTopNewsQuery $query): array
    {
        return $this->categoryFinder->findCategoriesWithTopArticles();
    }
}
