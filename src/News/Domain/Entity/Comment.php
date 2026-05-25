<?php

namespace App\News\Domain\Entity;

use App\News\Domain\Event\CommentPosted;
use App\News\Domain\Event\RecordsDomainEvents;
use App\News\Domain\Event\RecordsDomainEventsTrait;
use App\News\Domain\ValueObject\CommentAuthor;
use App\News\Domain\ValueObject\CommentContent;
use App\News\Domain\ValueObject\CommentID;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'comment')]
#[ORM\HasLifecycleCallbacks]
class Comment implements RecordsDomainEvents
{
    use RecordsDomainEventsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $author;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private DateTimeInterface $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id')]
    private Article $article;

    private function __construct()
    {
    }

    /**
     * @internal Only meant to be called from {@see Article::addComment()}.
     * Comment is a child entity of the Article aggregate; do not instantiate it from the outside.
     */
    public static function post(Article $article, CommentAuthor $author, CommentContent $content): self
    {
        $now = new DateTime();

        $comment = new self();
        $comment->article = $article;
        $comment->author = $author->value;
        $comment->content = $content->value;
        $comment->createdAt = $now;
        $comment->updatedAt = $now;

        return $comment;
    }

    public function edit(CommentContent $content): void
    {
        $this->content = $content->value;
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

    public function getId(): ?CommentID
    {
        return $this->id ? new CommentID($this->id) : null;
    }

    public function getAuthor(): CommentAuthor
    {
        return new CommentAuthor($this->author);
    }

    public function getContent(): CommentContent
    {
        return new CommentContent($this->content);
    }

    public function getArticle(): Article
    {
        return $this->article;
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

    #[ORM\PostPersist]
    public function onPersisted(): void
    {
        $commentID = $this->getId();
        $articleID = $this->article->getId();
        if ($commentID === null || $articleID === null) {
            return;
        }
        $this->recordThat(new CommentPosted($commentID, $articleID));
    }
}
