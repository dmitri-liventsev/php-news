<?php

namespace App\Identity\Domain\Exception;

use App\Identity\Domain\ValueObject\Email;
use App\Shared\Domain\Exception\DomainConflictException;

final class EmailAlreadyTakenException extends DomainConflictException
{
    public static function for(Email $email): self
    {
        return new self(sprintf('Email "%s" is already taken.', $email->value));
    }
}
