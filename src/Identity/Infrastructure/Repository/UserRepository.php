<?php

namespace App\Identity\Infrastructure\Repository;

use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Identity\Domain\ValueObject\Email;
use App\Identity\Domain\ValueObject\UserID;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findById(UserID $userID): ?User
    {
        return $this->find($userID->value);
    }

    public function findByEmail(Email $email): ?User
    {
        return $this->findOneBy(['email' => $email->value]);
    }

    public function save(User $user): UserID
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $id = $user->getId();
        if ($id === null) {
            throw new \RuntimeException('User ID is null after persist+flush.');
        }

        return $id;
    }
}
