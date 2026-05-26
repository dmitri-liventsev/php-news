<?php

namespace App\News\Domain\Exception;

use App\News\Domain\ValueObject\Email;
use DomainException;

final class EmailAlreadyTakenException extends DomainException
{
    public static function for(Email $email): self
    {
        return new self(sprintf('Email "%s" is already taken.', $email->value));
    }
}
