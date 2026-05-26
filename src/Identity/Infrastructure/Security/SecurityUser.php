<?php

namespace App\Identity\Infrastructure\Security;

use App\Identity\Domain\Entity\User;
use App\Identity\Domain\ValueObject\Role;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Symfony Security adapter wrapping a domain {@see User}. The domain entity has
 * no Symfony bindings — this object is what Symfony Security sees: it implements
 * the framework's UserInterface + PasswordAuthenticatedUserInterface and is the
 * class registered with `password_hashers`.
 *
 * It is a flat snapshot (email, hashed password, roles). Build one via
 * {@see fromDomain()} inside the {@see UserProvider}.
 */
final class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private readonly string $email,
        private string $hashedPassword,
        private readonly array $roles,
    ) {
    }

    public static function fromDomain(User $user): self
    {
        $roles = array_map(fn(Role $r) => $r->value, $user->getRoles());
        $roles[] = Role::USER->value;

        return new self(
            email: $user->getEmail()->value,
            hashedPassword: $user->getPassword()->value,
            roles: array_values(array_unique($roles)),
        );
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->hashedPassword;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    /**
     * @internal Only the UserProvider should mutate this — used when Symfony
     * Security asks to rehash the password after a successful login.
     */
    public function rehash(string $newHashedPassword): void
    {
        $this->hashedPassword = $newHashedPassword;
    }
}
