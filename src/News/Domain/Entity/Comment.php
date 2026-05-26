<?php

namespace App\News\Domain\Entity;

use App\News\Domain\Event\CommentPosted;
use App\News\Domain\ValueObject\CommentAuthor;
use App\News\Domain\ValueObject\CommentContent;
use App\News\Domain\ValueObject\CommentID;
use App\Shared\Domain\Event\RecordsDomainEvents;
use App\Shared\Domain\Event\RecordsDomainEventsTrait;
use App\Shared\Domain\SoftDeletable;
use App\Shared\Domain\Timestamped;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'comment')]
#[ORM\HasLifecycleCallbacks]
class Comment implements RecordsDomainEvents
{
    use RecordsDomainEventsTrait;
    use Timestamped;
    use SoftDeletable;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $author;

    #[ORM\Column(type: 'text')]
    private string $content;

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
        $comment = new self();
        $comment->article = $article;
        $comment->author = $author->value;
        $comment->content = $content->value;
        $comment->initTimestamps();

        return $comment;
    }

    public function edit(CommentContent $content): void
    {
        $this->content = $content->value;
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
