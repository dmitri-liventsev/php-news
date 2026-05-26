<?php

namespace App\News\Domain\Exception;

use App\News\Domain\ValueObject\Email;
use App\News\Domain\ValueObject\UserID;

final class UserNotFoundException extends EntityNotFoundException
{
    public static function byId(UserID $id): self
    {
        return new self(sprintf('User %d not found.', $id->value));
    }

    public static function byEmail(Email $email): self
    {
        return new self(sprintf('User with email "%s" not found.', $email->value));
    }
}
