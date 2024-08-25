<?php

namespace App\News\Application\Query;

use App\News\Domain\ValueObject\ArticleID;

readonly class GetCommentsByArticleQuery
{
    public function __construct(
        public ArticleID $articleID
    ) {}
}