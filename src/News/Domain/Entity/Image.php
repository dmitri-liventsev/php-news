<?php

namespace App\News\Domain\Entity;

use App\News\Domain\ValueObject\ImageFileName;
use App\News\Domain\ValueObject\ImageID;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'image')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $fileName;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private DateTimeInterface $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $deletedAt = null;

    private function __construct()
    {
    }

    public static function create(ImageFileName $fileName): self
    {
        $now = new DateTime();

        $image = new self();
        $image->fileName = $fileName->value;
        $image->createdAt = $now;
        $image->updatedAt = $now;

        return $image;
    }

    public function softDelete(): void
    {
        if ($this->deletedAt !== null) {
            return;
        }
        $this->deletedAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?ImageID
    {
        return $this->id ? new ImageID($this->id) : null;
    }

    public function getFileName(): ImageFileName
    {
        return new ImageFileName($this->fileName);
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }
}