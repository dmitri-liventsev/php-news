<?php

namespace App\News\Domain\Entity;

use App\News\Domain\ValueObject\ImageFileName;
use App\News\Domain\ValueObject\ImageID;
use App\Shared\Domain\SoftDeletable;
use App\Shared\Domain\Timestamped;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'image')]
class Image
{
    use Timestamped;
    use SoftDeletable;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $fileName;

    private function __construct()
    {
    }

    public static function create(ImageFileName $fileName): self
    {
        $image = new self();
        $image->fileName = $fileName->value;
        $image->initTimestamps();

        return $image;
    }

    public function getId(): ?ImageID
    {
        return $this->id ? new ImageID($this->id) : null;
    }

    public function getFileName(): ImageFileName
    {
        return new ImageFileName($this->fileName);
    }
}
