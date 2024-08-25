<?php

namespace App\News\Application\Query;

use App\News\Domain\ValueObject\CategoryID;

readonly class GetCategoryByIdQuery
{
    public function __construct(
        public CategoryID $categoryID
    ) {}
}