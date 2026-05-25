<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\Finder\CommentFinderInterface;
use App\News\Application\Query\GetCommentsByArticleQuery;

class GetCommentsByArticleHandler
{
    public function __construct(private readonly CommentFinderInterface $commentFinder)
    {
    }

    public function __invoke(GetCommentsByArticleQuery $query): array
    {
        return $this->commentFinder->findByArticle($query->articleID);
    }
}
