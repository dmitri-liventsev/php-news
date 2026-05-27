<?php

namespace App\Mudimind\Domain\Entity;

use App\Mudimind\Domain\ValueObject\MasseurID;
use App\Mudimind\Domain\ValueObject\MasseurName;
use App\Shared\Domain\SoftDeletable;
use App\Shared\Domain\Timestamped;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'masseur')]
class Masseur
{
    use Timestamped;
    use SoftDeletable;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    private function __construct()
    {
    }

    public static function create(MasseurName $name): self
    {
        $masseur = new self();
        $masseur->name = $name->value;
        $masseur->initTimestamps();

        return $masseur;
    }

    public function rename(MasseurName $name): void
    {
        $this->name = $name->value;
        $this->touch();
    }

    public function getId(): ?MasseurID
    {
        return $this->id !== null ? new MasseurID($this->id) : null;
    }

    public function getName(): MasseurName
    {
        return new MasseurName($this->name);
    }
}