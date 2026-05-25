<?php

namespace App\News\Domain\Event;

use App\News\Domain\ValueObject\ArticleID;

final readonly class ArticleMarkedAsTop
{
    public function __construct(public ArticleID $articleID)
    {
    }
}
