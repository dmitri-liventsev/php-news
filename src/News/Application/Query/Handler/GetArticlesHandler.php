<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\Finder\ArticleFinderInterface;
use App\News\Application\Query\GetArticlesQuery;

class GetArticlesHandler
{
    public function __construct(private readonly ArticleFinderInterface $articleFinder)
    {
    }

    public function __invoke(GetArticlesQuery $query): array
    {
        $page = max(1, $query->page);
        $offset = ($page - 1) * $query->limit;

        return $this->articleFinder->findRecentPage($query->limit, $offset);
    }
}
