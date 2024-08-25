<?php

namespace App\News\Application\Command;

use App\News\Domain\ValueObject\ArticleID;

readonly class IncreaseArticleNumberOfViewCommand
{
    public function __construct(
        public ArticleID $articleId,
    ) {}
}