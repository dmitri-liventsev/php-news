<?php

namespace App\News\Application\Command;

use App\News\Domain\ValueObject\ArticleID;

readonly class UpdateArticleCommand
{
    public function __construct(
        public ArticleID $articleID,
        public string    $title,
        public string    $shortDescription,
        public string    $content,
        public ?int      $imageID,
        public array     $categories,
    ) {
    }
}