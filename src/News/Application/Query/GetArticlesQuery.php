<?php

namespace App\News\Application\Query;

readonly class GetArticlesQuery
{
    public int $limit;
    public function __construct(
        public int $page
    ) {
        $this->limit = 10;
    }
}