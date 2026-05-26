<?php

namespace App\News\Domain\Entity;

use App\News\Domain\ValueObject\CategoryID;
use App\News\Domain\ValueObject\CategoryTitle;
use App\Shared\Domain\SoftDeletable;
use App\Shared\Domain\Timestamped;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category')]
class Category
{
    use Timestamped;
    use SoftDeletable;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'categories')]
    private Collection $articles;

    private function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public static function create(CategoryTitle $title): self
    {
        $category = new self();
        $category->title = $title->value;
        $category->initTimestamps();

        return $category;
    }

    public function rename(CategoryTitle $title): void
    {
        $this->title = $title->value;
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
     * @return iterable<Article>
     */
    public function getArticles(): iterable
    {
        return $this->articles;
    }
}
