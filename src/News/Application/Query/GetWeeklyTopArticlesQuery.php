<?php

namespace App\News\Application\Query;

readonly class GetWeeklyTopArticlesQuery
{
    public function __construct(
        public int $limit
    ) {}
}