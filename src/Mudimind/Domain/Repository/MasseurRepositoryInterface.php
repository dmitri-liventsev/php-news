<?php

namespace App\Mudimind\Domain\Repository;

use App\Mudimind\Domain\Entity\Masseur;
use App\Mudimind\Domain\ValueObject\MasseurID;

interface MasseurRepositoryInterface
{
    public function findById(MasseurID $id): ?Masseur;
}
