<?php

namespace App\Identity\Domain\Exception;

use App\Identity\Domain\ValueObject\Email;
use App\Identity\Domain\ValueObject\UserID;
use App\Shared\Domain\Exception\EntityNotFoundException;

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
