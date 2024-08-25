<?php

namespace App\News\Application\Query;

use App\News\Domain\ValueObject\ArticleID;

readonly class GetArticleByIdQuery
{
    public function __construct(
        public ArticleID $articleID,
    ) {}
}