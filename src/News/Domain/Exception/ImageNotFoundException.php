<?php

namespace App\News\Domain\Exception;

use App\News\Domain\ValueObject\ImageID;

final class ImageNotFoundException extends EntityNotFoundException
{
    public static function byId(ImageID $id): self
    {
        return new self(sprintf('Image %d not found.', $id->value));
    }
}
