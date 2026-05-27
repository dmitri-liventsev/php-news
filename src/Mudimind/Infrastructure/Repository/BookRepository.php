<?php

namespace App\Mudimind\Infrastructure\Repository;

use App\Mudimind\Domain\Entity\Book;
use App\Mudimind\Domain\Repository\BookRepositoryInterface;
use App\Mudimind\Domain\ValueObject\BookID;
use App\Mudimind\Domain\ValueObject\MasseurID;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

class BookRepository extends ServiceEntityRepository implements BookRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $book): BookID
    {
        $this->getEntityManager()->persist($book);
        $this->getEntityManager()->flush();

        $id = $book->getId();
        if ($id === null) {
            throw new RuntimeException('Book ID is null after persist+flush.');
        }

        return $id;
    }

    public function findByMasseurAndDay(MasseurID $masseurId, DateTimeInterface $day): array
    {
        $dayStart = DateTimeImmutable::createFromInterface($day)->setTime(0, 0);
        $nextDay = $dayStart->modify('+1 day');

        return $this->createQueryBuilder('b')
            ->andWhere('b.masseur = :masseurId')
            ->andWhere('b.startAt >= :dayStart')
            ->andWhere('b.startAt < :nextDay')
            ->setParameter('masseurId', $masseurId->value)
            ->setParameter('dayStart', $dayStart)
            ->setParameter('nextDay', $nextDay)
            ->getQuery()
            ->getResult();
    }
}