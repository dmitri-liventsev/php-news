<?php

namespace App\Mudimind\Domain\Exception;

use App\Mudimind\Domain\ValueObject\MasseurID;
use App\Shared\Domain\Exception\DomainConflictException;
use DateTimeInterface;

final class SpotNotAvailableException extends DomainConflictException
{
    public static function forMasseur(MasseurID $masseurId, DateTimeInterface $start, DateTimeInterface $end): self
    {
        return new self(sprintf(
            'Masseur %d is not available between %s and %s.',
            $masseurId->value,
            $start->format('Y-m-d H:i'),
            $end->format('Y-m-d H:i'),
        ));
    }
}
