<?php

namespace App\News\Application\Query;

use App\News\Domain\ValueObject\CategoryID;

readonly class GetArticlesByCategoryQuery
{
    readonly public int $limit;

    public function __construct(
        public CategoryID $categoryID,
        public int $page
    ) {
        $this->limit = 10;
    }
}