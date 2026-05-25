<?php

namespace App\News\Domain\Event;

use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CommentID;

final readonly class CommentPosted
{
    public function __construct(
        public CommentID $commentID,
        public ArticleID $articleID,
    ) {
    }
}
