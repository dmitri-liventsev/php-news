<?php

namespace App\News\Domain\Exception;

use App\News\Domain\ValueObject\ArticleID;
use App\Shared\Domain\Exception\EntityNotFoundException;

final class ArticleNotFoundException extends EntityNotFoundException
{
    public static function byId(ArticleID $id): self
    {
        return new self(sprintf('Article %d not found.', $id->value));
    }
}
