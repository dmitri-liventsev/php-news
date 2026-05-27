<?php

namespace App\Mudimind\Domain\Exception;

use App\Mudimind\Domain\ValueObject\MasseurID;
use App\Shared\Domain\Exception\EntityNotFoundException;

final class MasseurNotFoundException extends EntityNotFoundException
{
    public static function byId(MasseurID $id): self
    {
        return new self(sprintf('Masseur %d not found.', $id->value));
    }
}
