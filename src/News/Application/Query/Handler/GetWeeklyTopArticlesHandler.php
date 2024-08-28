<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetWeeklyTopArticlesQuery;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use DateInterval;
use DateTime;

class GetWeeklyTopArticlesHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository) {
        $this->articleRepository = $articleRepository;
    }

    public function __invoke(GetWeeklyTopArticlesQuery $query): array
    {
        $from = new DateTime();
        $interval = new DateInterval('P7D');
        $from->sub($interval);

        return $this->articleRepository->findTopArticles($from, $query->limit);
    }
}