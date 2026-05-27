<?php

namespace App\Mudimind\Application\Query\Handler;

use App\Mudimind\Application\Query\Finder\FreeSpotFinderInterface;
use App\Mudimind\Application\Query\GetFreeSpotsQuery;

class GetFreeSpotsHandler
{
    public function __construct(
        private readonly FreeSpotFinderInterface $freeSpotFinder,
    ) {
    }

    /**
     * @return \App\Mudimind\Application\Query\Handler\DTO\FreeSpotDTO[]
     */
    public function __invoke(GetFreeSpotsQuery $query): array
    {
        return $this->freeSpotFinder->findFreeSpots(
            $query->date,
            $query->durationMinutes,
            $query->masseurId,
        );
    }
}