<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetArticlesQuery;
use App\News\Domain\Entity\Article;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use Doctrine\Common\Collections\Collection;

class GetArticlesHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param GetArticlesQuery $query
     * @return Collection|Article[]
     */
    public function __invoke(GetArticlesQuery $query)
    {
        $offset = ($query->page - 1) * $query->limit;

        return $this->articleRepository->findWithPagination($query->limit, $offset);
    }
}