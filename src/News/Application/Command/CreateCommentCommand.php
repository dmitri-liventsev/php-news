<?php

namespace App\News\Application\Command;

use App\News\Domain\ValueObject\ArticleID;

readonly class CreateCommentCommand
{
    public function __construct(
        public ArticleID $articleID,
        public string    $content,
        public string    $author,
    ) {
    }
}