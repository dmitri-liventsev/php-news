<?php

namespace App\Tests\News\Domain\Entity;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Category;
use App\News\Domain\Entity\Comment;
use App\News\Domain\Event\CommentPosted;
use App\News\Domain\ValueObject\ArticleContent;
use App\News\Domain\ValueObject\ArticleTitle;
use App\News\Domain\ValueObject\CategoryTitle;
use App\News\Domain\ValueObject\CommentAuthor;
use App\News\Domain\ValueObject\CommentContent;
use App\News\Domain\ValueObject\ShortDescription;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class CommentTest extends TestCase
{
    public function testPostInitializesFields(): void
    {
        $article = $this->makeArticle();

        $comment = Comment::post($article, new CommentAuthor('Bob'), new CommentContent('Nice!'));

        $this->assertSame('Bob', $comment->getAuthor()->value);
        $this->assertSame('Nice!', $comment->getContent()->value);
        $this->assertSame($article, $comment->getArticle());
        $this->assertNull($comment->getDeletedAt());
        $this->assertSame($comment->getCreatedAt(), $comment->getUpdatedAt());
    }

    public function testEditChangesContentAndTouches(): void
    {
        $comment = Comment::post($this->makeArticle(), new CommentAuthor('Bob'), new CommentContent('first'));
        $beforeUpdate = $comment->getUpdatedAt();
        usleep(2000);

        $comment->edit(new CommentContent('second'));

        $this->assertSame('second', $comment->getContent()->value);
        $this->assertGreaterThan($beforeUpdate, $comment->getUpdatedAt());
    }

    public function testSoftDeleteIsIdempotent(): void
    {
        $comment = Comment::post($this->makeArticle(), new CommentAuthor('Bob'), new CommentContent('x'));

        $comment->softDelete();
        $first = $comment->getDeletedAt();
        $this->assertNotNull($first);

        usleep(2000);
        $comment->softDelete();
        $this->assertSame($first, $comment->getDeletedAt());
    }

    public function testOnPersistedRecordsCommentPosted(): void
    {
        $article = $this->makeArticle();
        $this->setId($article, 7);
        $comment = Comment::post($article, new CommentAuthor('Bob'), new CommentContent('x'));
        $this->setId($comment, 99);

        $comment->onPersisted();

        $events = $comment->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(CommentPosted::class, $events[0]);
        $this->assertSame(99, $events[0]->commentID->value);
        $this->assertSame(7, $events[0]->articleID->value);
    }

    public function testOnPersistedWithoutArticleIdIsSilent(): void
    {
        $comment = Comment::post($this->makeArticle(), new CommentAuthor('Bob'), new CommentContent('x'));
        $this->setId($comment, 99);

        $comment->onPersisted();

        $this->assertSame([], $comment->pullEvents());
    }

    private function makeArticle(): Article
    {
        return Article::create(
            new ArticleTitle('t'),
            new ShortDescription('s'),
            new ArticleContent('c'),
            null,
            [Category::create(new CategoryTitle('cat'))],
        );
    }

    private function setId(object $entity, int $id): void
    {
        (new ReflectionProperty($entity, 'id'))->setValue($entity, $id);
    }
}
