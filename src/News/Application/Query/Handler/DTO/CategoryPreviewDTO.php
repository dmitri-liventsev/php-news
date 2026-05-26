<?php

namespace App\News\Application\Query\Handler\DTO;

final class CategoryPreviewDTO
{
    public function __construct(
        public int $id,
        public string $title,
    ) {
    }
}
