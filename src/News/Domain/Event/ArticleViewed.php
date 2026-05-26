<?php

namespace App\News\Domain\Event;

use App\News\Domain\ValueObject\ArticleID;
use App\Shared\Domain\Event\AbstractDomainEvent;

final readonly class ArticleViewed extends AbstractDomainEvent
{
    public function __construct(public ArticleID $articleID)
    {
        parent::__construct();
    }
}
