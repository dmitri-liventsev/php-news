<?php

namespace App\News\Domain\Exception;

use App\News\Domain\ValueObject\CategoryID;
use App\Shared\Domain\Exception\EntityNotFoundException;

final class CategoryNotFoundException extends EntityNotFoundException
{
    public static function byId(CategoryID $id): self
    {
        return new self(sprintf('Category %d not found.', $id->value));
    }
}
