<?php

namespace App\News\Domain\Event;

use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;
use App\Shared\Domain\Event\AbstractDomainEvent;

final readonly class ArticleCreated extends AbstractDomainEvent
{
    /**
     * @param CategoryID[] $categoryIDs
     */
    public function __construct(
        public ArticleID $articleID,
        public array     $categoryIDs,
    ) {
        parent::__construct();
    }
}
