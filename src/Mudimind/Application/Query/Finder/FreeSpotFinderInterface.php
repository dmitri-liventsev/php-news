<?php

namespace App\Mudimind\Application\Query\Finder;

use App\Mudimind\Application\Query\Handler\DTO\FreeSpotDTO;
use App\Mudimind\Domain\ValueObject\MasseurID;
use DateTimeImmutable;

interface FreeSpotFinderInterface
{
    /**
     * Free slots of the requested duration on the given day, optionally for a
     * single masseur. When $masseurId is null, slots across all masseurs are returned.
     *
     * @return FreeSpotDTO[]
     */
    public function findFreeSpots(DateTimeImmutable $date, int $durationMinutes, ?MasseurID $masseurId): array;
}
