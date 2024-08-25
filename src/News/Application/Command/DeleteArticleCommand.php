<?php

namespace App\News\Application\Command;

use App\News\Domain\ValueObject\ArticleID;

readonly class DeleteArticleCommand
{
    public function __construct(
        public ArticleID $articleId,
    ) {}
}