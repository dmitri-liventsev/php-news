<?php

namespace App\News\Application\Query\Handler\DTO;

final class CommentDTO
{
    public function __construct(
        public int $id,
        public string $author,
        public string $content,
    ) {
    }
}
