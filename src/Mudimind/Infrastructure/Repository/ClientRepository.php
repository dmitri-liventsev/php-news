<?php

namespace App\Mudimind\Infrastructure\Repository;

use App\Mudimind\Domain\Entity\Client;
use App\Mudimind\Domain\Repository\ClientRepositoryInterface;
use App\Mudimind\Domain\ValueObject\Email;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findByEmail(Email $email): ?Client
    {
        return $this->findOneBy(['email' => $email->value]);
    }

    public function save(Client $client): Client
    {
        $this->getEntityManager()->persist($client);
        $this->getEntityManager()->flush();

        return $client;
    }
}