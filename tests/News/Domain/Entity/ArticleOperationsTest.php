<?php

namespace App\Tests\News\Domain\Entity;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Category;
use App\News\Domain\Entity\Image;
use App\News\Domain\ValueObject\ArticleContent;
use App\News\Domain\ValueObject\ArticleTitle;
use App\News\Domain\ValueObject\CategoryTitle;
use App\News\Domain\ValueObject\ImageFileName;
use App\News\Domain\ValueObject\ShortDescription;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class ArticleOperationsTest extends TestCase
{
    public function testCreateInitializesDefaults(): void
    {
        $article = $this->makeArticle();

        $this->assertSame('title', $article->getTitle()->value);
        $this->assertSame('short description', $article->getShortDescription()->value);
        $this->assertSame('content', $article->getContent()->value);
        $this->assertSame(0, $article->getNumberOfViews());
        $this->assertFalse($article->isTop());
        $this->assertNull($article->getDeletedAt());
        $this->assertSame($article->getCreatedAt(), $article->getUpdatedAt());
    }

    public function testRenameUpdatesTitleAndTouches(): void
    {
        $article = $this->makeArticle();
        $oldUpdatedAt = $article->getUpdatedAt();
        usleep(2000);

        $article->rename(new ArticleTitle('renamed'));

        $this->assertSame('renamed', $article->getTitle()->value);
        $this->assertGreaterThan($oldUpdatedAt, $article->getUpdatedAt());
    }

    public function testChangeShortDescription(): void
    {
        $article = $this->makeArticle();

        $article->changeShortDescription(new ShortDescription('new short'));

        $this->assertSame('new short', $article->getShortDescription()->value);
    }

    public function testRewriteContent(): void
    {
        $article = $this->makeArticle();

        $article->rewriteContent(new ArticleContent('new body'));

        $this->assertSame('new body', $article->getContent()->value);
    }

    public function testChangeImageAcceptsNullAndImage(): void
    {
        $article = $this->makeArticle();
        $image = Image::create(new ImageFileName('cover.jpg'));

        $article->changeImage($image);
        $this->assertSame($image, $article->getImage());

        $article->changeImage(null);
        $this->assertNull($article->getImage());
    }

    public function testAssignToCategoriesReplacesAll(): void
    {
        $original = $this->makeCategory('original');
        $article = $this->makeArticle($original);

        $a = $this->makeCategory('a');
        $b = $this->makeCategory('b');
        $article->assignToCategories($a, $b);

        $titles = array_map(
            fn(Category $c) => $c->getTitle()->value,
            $article->getCategories()->toArray(),
        );
        $this->assertSame(['a', 'b'], $titles);
    }

    public function testAssignToCategoriesDeduplicates(): void
    {
        $article = $this->makeArticle();
        $cat = $this->makeCategory('one');

        $article->assignToCategories($cat, $cat);

        $this->assertSame(1, $article->getCategories()->count());
    }

    public function testIncrementViewsIncreasesCounter(): void
    {
        $article = $this->makeArticle();

        $article->incrementViews();
        $article->incrementViews();

        $this->assertSame(2, $article->getNumberOfViews());
    }

    public function testMarkAsTopIsIdempotent(): void
    {
        $article = $this->makeArticle();
        $this->setId($article, 1);

        $article->markAsTop();
        $touchAfterFirst = $article->getUpdatedAt();
        usleep(2000);
        $article->markAsTop();

        $this->assertSame($touchAfterFirst, $article->getUpdatedAt(), 'second markAsTop must be no-op');
    }

    public function testUnmarkAsTopRequiresPriorMark(): void
    {
        $article = $this->makeArticle();
        $this->setId($article, 1);

        $article->unmarkAsTop();

        $this->assertFalse($article->isTop());
    }

    public function testSoftDeleteIsIdempotent(): void
    {
        $article = $this->makeArticle();

        $article->softDelete();
        $firstDeletedAt = $article->getDeletedAt();
        $this->assertNotNull($firstDeletedAt);

        usleep(2000);
        $article->softDelete();
        $this->assertSame($firstDeletedAt, $article->getDeletedAt(), 'second softDelete must be no-op');
    }

    private function makeArticle(?Category $category = null): Article
    {
        return Article::create(
            new ArticleTitle('title'),
            new ShortDescription('short description'),
            new ArticleContent('content'),
            null,
            [$category ?? $this->makeCategory()],
        );
    }

    private function makeCategory(string $title = 'cat'): Category
    {
        return Category::create(new CategoryTitle($title));
    }

    private function setId(object $entity, int $id): void
    {
        (new ReflectionProperty($entity, 'id'))->setValue($entity, $id);
    }
}
