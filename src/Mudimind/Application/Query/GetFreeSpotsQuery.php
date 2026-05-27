<?php

namespace App\Mudimind\Application\Query;

use App\Mudimind\Domain\ValueObject\MasseurID;
use DateTimeImmutable;

readonly class GetFreeSpotsQuery
{
    public function __construct(
        public DateTimeImmutable $date,
        public int $durationMinutes,
        public ?MasseurID $masseurId = null,
    ) {
    }
}
