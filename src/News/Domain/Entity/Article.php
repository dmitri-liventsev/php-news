<?php

namespace App\News\Domain\Entity;

use App\News\Domain\ValueObject\ArticleID;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'article')]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 500, name: 'short_description')]
    private string $shortDescription;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\ManyToOne(targetEntity: Image::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'id')]
    private ?Image $image = null;

    #[ORM\Column(type: 'integer', name: 'number_of_views')]
    private int $numberOfViews;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isTop;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'articles', cascade: ['remove'])]
    #[ORM\JoinTable(
        name: 'article_category',
        joinColumns: [new ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
    )]
    private Collection $categories;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class, cascade: ['persist', 'remove'])]
    private Collection $comments;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->isTop = false; // Значение по умолчанию
    }

    public function getId(): ?ArticleID
    {
        return $this->id ? new ArticleID($this->id) : null;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getNumberOfViews(): int
    {
        return $this->numberOfViews;
    }

    public function setNumberOfViews(int $numberOfViews): self
    {
        $this->numberOfViews = $numberOfViews;
        return $this;
    }

    public function getIsTop(): bool
    {
        return $this->isTop;
    }

    public function setIsTop(bool $isTop): self
    {
        $this->isTop = $isTop;
        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }
        return $this;
    }

    /**
     * @param Collection|Category[] $categories
     * @return $this
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);
        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // Set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
}
