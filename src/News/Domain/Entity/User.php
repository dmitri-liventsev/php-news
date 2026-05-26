<?php

namespace App\News\Domain\Entity;

use App\News\Domain\Event\RecordsDomainEvents;
use App\News\Domain\Event\RecordsDomainEventsTrait;
use App\News\Domain\Event\UserRegistered;
use App\News\Domain\ValueObject\Email;
use App\News\Domain\ValueObject\HashedPassword;
use App\News\Domain\ValueObject\Role;
use App\News\Domain\ValueObject\UserID;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface, RecordsDomainEvents
{
    use RecordsDomainEventsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    private function __construct()
    {
    }

    public static function register(Email $email, HashedPassword $password): self
    {
        $user = new self();
        $user->email = $email->value;
        $user->password = $password->value;
        $user->roles = [];

        return $user;
    }

    public function changePassword(HashedPassword $password): void
    {
        $this->password = $password->value;
    }

    public function grantRole(Role $role): void
    {
        if (in_array($role->value, $this->roles, true)) {
            return;
        }
        $this->roles[] = $role->value;
    }

    public function revokeRole(Role $role): void
    {
        $this->roles = array_values(array_filter(
            $this->roles,
            static fn(string $r) => $r !== $role->value,
        ));
    }

    public function getId(): ?UserID
    {
        return $this->id ? new UserID($this->id) : null;
    }

    public function getEmail(): Email
    {
        return new Email($this->email);
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = Role::USER->value;

        return array_values(array_unique($roles));
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function eraseCredentials(): void
    {
    }

    #[ORM\PostPersist]
    public function onPersisted(): void
    {
        $id = $this->getId();
        if ($id === null) {
            return;
        }
        $this->recordThat(new UserRegistered($id, $this->getEmail()));
    }
}
