<?php

namespace App\News\Application\Command;

readonly class CreateArticleCommand
{
    public function __construct(
        public string $title,
        public string $shortDescription,
        public string $content,
        public ?int   $imageID,
        public array  $categories,
    ) {
    }
}