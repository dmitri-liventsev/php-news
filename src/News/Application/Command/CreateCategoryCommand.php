<?php

namespace App\News\Application\Command;

readonly class CreateCategoryCommand
{
    public function __construct(
        public string $title,
    ) {
    }
}