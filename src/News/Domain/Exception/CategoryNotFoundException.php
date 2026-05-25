<?php

namespace App\News\Domain\Exception;

use App\News\Domain\ValueObject\CategoryID;

final class CategoryNotFoundException extends EntityNotFoundException
{
    public static function byId(CategoryID $id): self
    {
        return new self(sprintf('Category %d not found.', $id->value));
    }
}
