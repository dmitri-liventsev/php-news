<?php

namespace App\News\Domain\Exception;

use App\News\Domain\ValueObject\CommentID;

final class CommentNotFoundException extends EntityNotFoundException
{
    public static function byId(CommentID $id): self
    {
        return new self(sprintf('Comment %d not found.', $id->value));
    }
}
