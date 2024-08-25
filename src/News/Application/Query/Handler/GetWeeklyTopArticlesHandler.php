<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetWeeklyTopArticlesQuery;
use App\News\Domain\Entity\Article;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use Doctrine\Common\Collections\Collection;

class GetWeeklyTopArticlesHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param GetWeeklyTopArticlesQuery $query
     * @return Collection|Article[]
     */
    public function __invoke(GetWeeklyTopArticlesQuery $query)
    {
        $from = new \DateTime();
        $interval = new \DateInterval('P7D');
        $from->sub($interval);

        return $this->articleRepository->findTopArticles($from, $query->limit);
    }
}