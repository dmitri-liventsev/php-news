<?php

namespace App\News\Application\Command;

use App\News\Domain\ValueObject\CommentID;

readonly class DeleteCommentCommand
{
    public function __construct(
        public CommentID $commentID,
    ) {}
}