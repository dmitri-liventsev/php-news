<?php

namespace App\Identity\Infrastructure\Security;

use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Identity\Domain\ValueObject\Email;
use App\Identity\Domain\ValueObject\HashedPassword;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Loads a domain User from the repository and exposes it to Symfony Security
 * as a {@see SecurityUser} adapter. Also implements PasswordUpgraderInterface
 * so transparent password rehashing keeps working without leaking Security
 * concerns into the domain repository.
 *
 * @implements UserProviderInterface<SecurityUser>
 */
final readonly class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            $email = new Email($identifier);
        } catch (InvalidArgumentException) {
            throw new UserNotFoundException(sprintf('Invalid user identifier "%s".', $identifier));
        }

        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            throw new UserNotFoundException(sprintf('User with email "%s" not found.', $email->value));
        }

        return SecurityUser::fromDomain($user);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof SecurityUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === SecurityUser::class || is_subclass_of($class, SecurityUser::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof SecurityUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $domainUser = $this->userRepository->findByEmail(new Email($user->getUserIdentifier()));
        if ($domainUser === null) {
            return;
        }

        $domainUser->changePassword(new HashedPassword($newHashedPassword));
        $this->userRepository->save($domainUser);
        $user->rehash($newHashedPassword);
    }
}
