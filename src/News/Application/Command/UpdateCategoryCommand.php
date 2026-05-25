<?php

namespace App\News\Application\Command;

use App\News\Domain\ValueObject\CategoryID;

readonly class UpdateCategoryCommand
{
    public function __construct(
        public CategoryID $categoryID,
        public string     $title,
    ) {
    }
}