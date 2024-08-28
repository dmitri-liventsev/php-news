<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetArticlesQuery;
use App\News\Application\Query\Handler\DTO\ArticleDTO;
use App\News\Domain\Repository\ArticleRepositoryInterface;

class GetArticlesHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository) {
        $this->articleRepository = $articleRepository;
    }

    public function __invoke(GetArticlesQuery $query): array
    {
        $offset = ($query->page - 1) * $query->limit;

        $articles = $this->articleRepository->findWithPagination($query->limit, $offset);
        return array_map(fn ($article) => new ArticleDTO($article), $articles);
    }
}