<?php

namespace App\Identity\Domain\Entity;

use App\Identity\Domain\Event\UserRegistered;
use App\Identity\Domain\ValueObject\Email;
use App\Identity\Domain\ValueObject\HashedPassword;
use App\Identity\Domain\ValueObject\Role;
use App\Identity\Domain\ValueObject\UserID;
use App\Shared\Domain\Event\RecordsDomainEvents;
use App\Shared\Domain\Event\RecordsDomainEventsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
#[ORM\HasLifecycleCallbacks]
class User implements RecordsDomainEvents
{
    use RecordsDomainEventsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    /** @var string[] Stored as JSON; expose as Role[] through {@see getRoles()}. */
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

    public function getPassword(): HashedPassword
    {
        return new HashedPassword($this->password);
    }

    /**
     * Roles explicitly granted to this user. Symfony's baseline `ROLE_USER`
     * is added by the Infrastructure adapter, not here.
     *
     * @return Role[]
     */
    public function getRoles(): array
    {
        return array_values(array_filter(array_map(
            static fn(string $r) => Role::tryFrom($r),
            $this->roles,
        )));
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
