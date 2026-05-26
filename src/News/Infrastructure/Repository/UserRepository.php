<?php

namespace App\News\Infrastructure\Repository;

use App\News\Domain\Entity\User;
use App\News\Domain\Repository\UserRepositoryInterface;
use App\News\Domain\ValueObject\Email;
use App\News\Domain\ValueObject\HashedPassword;
use App\News\Domain\ValueObject\UserID;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface, PasswordUpgraderInterface
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

    /**
     * Used by Symfony Security to upgrade (rehash) the user's password automatically.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->changePassword(new HashedPassword($newHashedPassword));
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
