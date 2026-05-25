<?php

namespace App\News\Domain\Event;

use App\News\Domain\ValueObject\ArticleID;

final readonly class ArticleUnmarkedFromTop
{
    public function __construct(public ArticleID $articleID)
    {
    }
}
