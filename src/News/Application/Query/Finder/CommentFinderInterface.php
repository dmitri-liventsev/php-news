<?php

namespace App\News\Application\Query\Finder;

use App\News\Application\Query\Handler\DTO\CommentDTO;
use App\News\Domain\ValueObject\ArticleID;

interface CommentFinderInterface
{
    /**
     * @return CommentDTO[]
     */
    public function findByArticle(ArticleID $articleID): array;
}
