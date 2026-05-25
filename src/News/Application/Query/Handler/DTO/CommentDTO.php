<?php

namespace App\News\Application\Query\Handler\DTO;

use App\News\Domain\Entity\Comment;

class CommentDTO
{
    public int $id;
    public string $author;
    public string $content;

    public function __construct(Comment $comment = null) {
        $this->id = $comment?->getId()->value;
        $this->author = $comment?->getAuthor()->value;
        $this->content = $comment?->getContent()->value;
    }
}