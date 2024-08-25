<?php

namespace App\News\Domain\Repository;

use App\News\Domain\Entity\Comment;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CommentID;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): CommentID;

    public function deleteById(CommentID $commentID): void;

    public function findByArticle(ArticleID $articleID): ?Comment;
}