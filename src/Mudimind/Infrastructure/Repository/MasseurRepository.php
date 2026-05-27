<?php

namespace App\Mudimind\Infrastructure\Repository;

use App\Mudimind\Domain\Entity\Masseur;
use App\Mudimind\Domain\Repository\MasseurRepositoryInterface;
use App\Mudimind\Domain\ValueObject\MasseurID;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MasseurRepository extends ServiceEntityRepository implements MasseurRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Masseur::class);
    }

    public function findById(MasseurID $id): ?Masseur
    {
        return $this->find($id->value);
    }
}