<?php

namespace App\News\Domain\Repository;

use App\News\Domain\Entity\Comment;
use App\News\Domain\ValueObject\CommentID;

interface CommentRepositoryInterface
{
    public function findById(CommentID $commentID): ?Comment;
}
