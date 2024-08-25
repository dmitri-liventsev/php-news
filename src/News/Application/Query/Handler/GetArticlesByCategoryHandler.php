<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetArticlesByCategoryQuery;
use App\News\Domain\Repository\ArticleRepositoryInterface;

class GetArticlesByCategoryHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository) {
        $this->articleRepository = $articleRepository;
    }

    public function __invoke(GetArticlesByCategoryQuery $query): array
    {
        $offset = ($query->page - 1) * $query->limit;

        return $this->articleRepository->findByCategoryWithPagination($query->categoryID, $query->limit, $offset);
    }
}