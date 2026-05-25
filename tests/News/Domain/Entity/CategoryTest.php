<?php

namespace App\Tests\News\Domain\Entity;

use App\News\Domain\Entity\Category;
use App\News\Domain\ValueObject\CategoryTitle;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testCreateInitializes(): void
    {
        $category = Category::create(new CategoryTitle('Sports'));

        $this->assertSame('Sports', $category->getTitle()->value);
        $this->assertNull($category->getDeletedAt());
        $this->assertSame($category->getCreatedAt(), $category->getUpdatedAt());
        $this->assertCount(0, $category->getArticles());
    }

    public function testRenameUpdatesTitleAndTouches(): void
    {
        $category = Category::create(new CategoryTitle('Old'));
        $beforeUpdate = $category->getUpdatedAt();
        usleep(2000);

        $category->rename(new CategoryTitle('New'));

        $this->assertSame('New', $category->getTitle()->value);
        $this->assertGreaterThan($beforeUpdate, $category->getUpdatedAt());
    }

    public function testSoftDeleteIsIdempotent(): void
    {
        $category = Category::create(new CategoryTitle('Sports'));

        $category->softDelete();
        $first = $category->getDeletedAt();
        $this->assertNotNull($first);

        usleep(2000);
        $category->softDelete();
        $this->assertSame($first, $category->getDeletedAt());
    }
}
