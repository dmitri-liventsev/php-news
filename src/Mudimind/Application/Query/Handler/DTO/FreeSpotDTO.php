<?php

namespace App\Mudimind\Application\Query\Handler\DTO;

/**
 * Read-side projection of a bookable slot for a given masseur.
 * Pure data — primitives only, marshaled to JSON at the HTTP boundary.
 */
final readonly class FreeSpotDTO
{
    public function __construct(
        public int $masseurId,
        public string $masseurName,
        public string $startAt,
        public string $endAt,
    ) {
    }
}