<?php

namespace App\News\Domain\Event;

use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CommentID;
use App\Shared\Domain\Event\AbstractDomainEvent;

final readonly class CommentPosted extends AbstractDomainEvent
{
    public function __construct(
        public CommentID $commentID,
        public ArticleID $articleID,
    ) {
        parent::__construct();
    }
}
