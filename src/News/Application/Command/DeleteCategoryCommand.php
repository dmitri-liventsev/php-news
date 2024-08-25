<?php

namespace App\News\Application\Command;

use App\News\Domain\ValueObject\CategoryID;

readonly class DeleteCategoryCommand
{
    public function __construct(
        public CategoryID $categoryID,
    ) {}
}