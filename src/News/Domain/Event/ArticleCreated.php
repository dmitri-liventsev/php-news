<?php

namespace App\News\Domain\Event;

use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;

final readonly class ArticleCreated
{
    /**
     * @param CategoryID[] $categoryIDs
     */
    public function __construct(
        public ArticleID $articleID,
        public array     $categoryIDs,
    ) {
    }
}
