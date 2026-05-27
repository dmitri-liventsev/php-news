<?php

namespace App\Mudimind\Domain\Entity;

use App\Mudimind\Domain\ValueObject\ClientID;
use App\Mudimind\Domain\ValueObject\Email;
use App\Shared\Domain\SoftDeletable;
use App\Shared\Domain\Timestamped;
use Doctrine\ORM\Mapping as ORM;

/**
 * A walk-in or returning visitor, identified by email. Created lazily the first
 * time someone books a massage with that address.
 */
#[ORM\Entity]
#[ORM\Table(name: 'client')]
class Client
{
    use Timestamped;
    use SoftDeletable;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name = null;

    private function __construct()
    {
    }

    public static function register(Email $email, ?string $name = null): self
    {
        $client = new self();
        $client->email = $email->value;
        $client->name = $name;
        $client->initTimestamps();

        return $client;
    }

    public function getId(): ?ClientID
    {
        return $this->id !== null ? new ClientID($this->id) : null;
    }

    public function getEmail(): Email
    {
        return new Email($this->email);
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}