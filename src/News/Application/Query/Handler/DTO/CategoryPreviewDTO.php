<?php

namespace App\News\Application\Query\Handler\DTO;

use App\News\Domain\Entity\Category;

class CategoryPreviewDTO
{
    public int $id;
    public string $title;

    public function __construct(Category $category = null) {
        $this->id = $category?->getId()->getValue();
        $this->title = $category?->getTitle();
    }
}