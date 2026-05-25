<?php

namespace App\Tests\News\Domain\Entity;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Category;
use App\News\Domain\Event\ArticleCreated;
use App\News\Domain\Event\ArticleMarkedAsTop;
use App\News\Domain\Event\ArticleUnmarkedFromTop;
use App\News\Domain\Event\ArticleViewed;
use App\News\Domain\ValueObject\ArticleContent;
use App\News\Domain\ValueObject\ArticleTitle;
use App\News\Domain\ValueObject\CategoryTitle;
use App\News\Domain\ValueObject\ShortDescription;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class ArticleEventsTest extends TestCase
{
    public function testFreshlyCreatedArticleHasNoEvents(): void
    {
        $article = $this->makeArticle();

        $this->assertSame([], $article->pullEvents());
    }

    public function testOnPersistedRecordsArticleCreatedWithCategoryIds(): void
    {
        $category = $this->makeCategory(7);
        $article = $this->makeArticle($category);
        $this->setId($article, 42);

        $article->onPersisted();

        $events = $article->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ArticleCreated::class, $events[0]);
        $this->assertSame(42, $events[0]->articleID->value);
        $this->assertCount(1, $events[0]->categoryIDs);
        $this->assertSame(7, $events[0]->categoryIDs[0]->value);
    }

    public function testIncrementViewsRecordsArticleViewed(): void
    {
        $article = $this->makeArticle();
        $this->setId($article, 10);
        $article->pullEvents(); // drain anything

        $article->incrementViews();

        $events = $article->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ArticleViewed::class, $events[0]);
        $this->assertSame(10, $events[0]->articleID->value);
    }

    public function testIncrementViewsBeforePersistIsSilent(): void
    {
        $article = $this->makeArticle();

        $article->incrementViews();

        $this->assertSame([], $article->pullEvents());
    }

    public function testMarkAsTopRecordsEventOnlyOnTransition(): void
    {
        $article = $this->makeArticle();
        $this->setId($article, 5);

        $article->markAsTop();
        $events = $article->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ArticleMarkedAsTop::class, $events[0]);

        $article->markAsTop();
        $this->assertSame([], $article->pullEvents(), 'no event on no-op');
    }

    public function testUnmarkAsTopRecordsEventOnlyOnTransition(): void
    {
        $article = $this->makeArticle();
        $this->setId($article, 5);
        $article->markAsTop();
        $article->pullEvents();

        $article->unmarkAsTop();
        $events = $article->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ArticleUnmarkedFromTop::class, $events[0]);

        $article->unmarkAsTop();
        $this->assertSame([], $article->pullEvents());
    }

    private function makeArticle(?Category $category = null): Article
    {
        return Article::create(
            new ArticleTitle('t'),
            new ShortDescription('s'),
            new ArticleContent('c'),
            null,
            $category ? [$category] : [$this->makeCategory()],
        );
    }

    private function makeCategory(int $id = 1): Category
    {
        $category = Category::create(new CategoryTitle('cat'));
        $this->setId($category, $id);

        return $category;
    }

    private function setId(object $entity, int $id): void
    {
        $property = new ReflectionProperty($entity, 'id');
        $property->setValue($entity, $id);
    }
}
