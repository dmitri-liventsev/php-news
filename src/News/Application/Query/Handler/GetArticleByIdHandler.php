<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\Finder\ArticleFinderInterface;
use App\News\Application\Query\GetArticleByIdQuery;
use App\News\Application\Query\Handler\DTO\ArticleDTO;

class GetArticleByIdHandler
{
    public function __construct(private readonly ArticleFinderInterface $articleFinder)
    {
    }

    public function __invoke(GetArticleByIdQuery $query): ?ArticleDTO
    {
        return $this->articleFinder->findOneById($query->articleID);
    }
}
