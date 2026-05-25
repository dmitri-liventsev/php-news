<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\Finder\ArticleFinderInterface;
use App\News\Application\Query\GetWeeklyTopArticlesQuery;
use DateInterval;
use DateTime;

class GetWeeklyTopArticlesHandler
{
    public function __construct(private readonly ArticleFinderInterface $articleFinder)
    {
    }

    public function __invoke(GetWeeklyTopArticlesQuery $query): array
    {
        $from = (new DateTime())->sub(new DateInterval('P7D'));

        return $this->articleFinder->findTopSince($from, $query->limit);
    }
}
