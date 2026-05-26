<?php

namespace App\News\Domain\Entity;

use App\News\Domain\Event\ArticleCreated;
use App\News\Domain\Event\ArticleMarkedAsTop;
use App\News\Domain\Event\ArticleUnmarkedFromTop;
use App\News\Domain\Event\ArticleViewed;
use App\News\Domain\ValueObject\ArticleContent;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\ArticleTitle;
use App\News\Domain\ValueObject\CommentAuthor;
use App\News\Domain\ValueObject\CommentContent;
use App\News\Domain\ValueObject\CommentID;
use App\News\Domain\ValueObject\ShortDescription;
use App\Shared\Domain\Event\RecordsDomainEvents;
use App\Shared\Domain\Event\RecordsDomainEventsTrait;
use App\Shared\Domain\SoftDeletable;
use App\Shared\Domain\Timestamped;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'article')]
#[ORM\HasLifecycleCallbacks]
class Article implements RecordsDomainEvents
{
    use RecordsDomainEventsTrait;
    use Timestamped;
    use SoftDeletable;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(name: 'short_description', type: 'string', length: 500)]
    private string $shortDescription;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\ManyToOne(targetEntity: Image::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'id')]
    private ?Image $image = null;

    #[ORM\Column(name: 'number_of_views', type: 'integer')]
    private int $numberOfViews;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isTop;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'articles')]
    #[ORM\JoinTable(
        name: 'article_category',
        joinColumns: [new ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
    )]
    private Collection $categories;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'article', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $comments;

    private function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->isTop = false;
        $this->numberOfViews = 0;
    }

    /**
     * @param iterable<Category> $categories
     */
    public static function create(
        ArticleTitle $title,
        ShortDescription $shortDescription,
        ArticleContent $content,
        ?Image $image,
        iterable $categories,
    ): self {
        $article = new self();
        $article->title = $title->value;
        $article->shortDescription = $shortDescription->value;
        $article->content = $content->value;
        $article->image = $image;
        $article->initTimestamps();

        foreach ($categories as $category) {
            if (!$article->categories->contains($category)) {
                $article->categories[] = $category;
            }
        }

        return $article;
    }

    public function rename(ArticleTitle $title): void
    {
        $this->title = $title->value;
        $this->touch();
    }

    public function changeShortDescription(ShortDescription $shortDescription): void
    {
        $this->shortDescription = $shortDescription->value;
        $this->touch();
    }

    public function rewriteContent(ArticleContent $content): void
    {
        $this->content = $content->value;
        $this->touch();
    }

    public function changeImage(?Image $image): void
    {
        $this->image = $image;
        $this->touch();
    }

    public function assignToCategories(Category ...$categories): void
    {
        $this->categories->clear();
        foreach ($categories as $category) {
            if (!$this->categories->contains($category)) {
                $this->categories[] = $category;
            }
        }
        $this->touch();
    }

    public function incrementViews(): void
    {
        $this->numberOfViews++;
        if (($id = $this->getId()) !== null) {
            $this->recordThat(new ArticleViewed($id));
        }
    }

    public function markAsTop(): void
    {
        if ($this->isTop) {
            return;
        }
        $this->isTop = true;
        $this->touch();
        if (($id = $this->getId()) !== null) {
            $this->recordThat(new ArticleMarkedAsTop($id));
        }
    }

    public function unmarkAsTop(): void
    {
        if (!$this->isTop) {
            return;
        }
        $this->isTop = false;
        $this->touch();
        if (($id = $this->getId()) !== null) {
            $this->recordThat(new ArticleUnmarkedFromTop($id));
        }
    }

    public function addComment(CommentAuthor $author, CommentContent $content): Comment
    {
        $comment = Comment::post($this, $author, $content);
        $this->comments[] = $comment;

        return $comment;
    }

    public function removeComment(CommentID $commentID): void
    {
        foreach ($this->comments as $comment) {
            if (($id = $comment->getId()) !== null && $id->equals($commentID)) {
                $this->comments->removeElement($comment);
                return;
            }
        }
    }

    public function getId(): ?ArticleID
    {
        return $this->id ? new ArticleID($this->id) : null;
    }

    public function getTitle(): ArticleTitle
    {
        return new ArticleTitle($this->title);
    }

    public function getShortDescription(): ShortDescription
    {
        return new ShortDescription($this->shortDescription);
    }

    public function getContent(): ArticleContent
    {
        return new ArticleContent($this->content);
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function getNumberOfViews(): int
    {
        return $this->numberOfViews;
    }

    public function isTop(): bool
    {
        return $this->isTop;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    #[ORM\PostPersist]
    public function onPersisted(): void
    {
        if ($this->id === null) {
            return;
        }
        $categoryIDs = [];
        foreach ($this->categories as $category) {
            $categoryID = $category->getId();
            if ($categoryID !== null) {
                $categoryIDs[] = $categoryID;
            }
        }
        $this->recordThat(new ArticleCreated($this->getId(), $categoryIDs));
    }
}
