<?php

namespace App\News\Domain\Entity;

use App\News\Domain\ValueObject\CategoryID;
use App\News\Domain\ValueObject\CategoryTitle;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'categories')]
    private Collection $articles;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private DateTimeInterface $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $deletedAt = null;

    private function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public static function create(CategoryTitle $title): self
    {
        $now = new DateTime();

        $category = new self();
        $category->title = $title->value;
        $category->createdAt = $now;
        $category->updatedAt = $now;

        return $category;
    }

    public function rename(CategoryTitle $title): void
    {
        $this->title = $title->value;
        $this->touch();
    }

    public function softDelete(): void
    {
        if ($this->deletedAt !== null) {
            return;
        }
        $this->deletedAt = new DateTime();
        $this->touch();
    }

    public function getId(): ?CategoryID
    {
        return $this->id ? new CategoryID($this->id) : null;
    }

    public function getTitle(): CategoryTitle
    {
        return new CategoryTitle($this->title);
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
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

    private function touch(): void
    {
        $this->updatedAt = new DateTime();
    }
}