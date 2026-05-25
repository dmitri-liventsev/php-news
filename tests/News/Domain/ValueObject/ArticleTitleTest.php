<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\ArticleTitle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ArticleTitleTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $title = new ArticleTitle('Breaking news');

        $this->assertSame('Breaking news', $title->value);
        $this->assertSame('Breaking news', (string) $title);
    }

    public function testTrimsSurroundingWhitespace(): void
    {
        $title = new ArticleTitle("  Breaking news\n");

        $this->assertSame('Breaking news', $title->value);
    }

    public function testRejectsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ArticleTitle('');
    }

    public function testRejectsWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ArticleTitle("   \t\n");
    }

    public function testRejectsValueExceeding255Characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ArticleTitle(str_repeat('a', 256));
    }

    public function testAcceptsExactly255Characters(): void
    {
        $value = str_repeat('a', 255);

        $title = new ArticleTitle($value);

        $this->assertSame($value, $title->value);
    }

    public function testEqualsByValue(): void
    {
        $a = new ArticleTitle('Same');
        $b = new ArticleTitle('Same');
        $c = new ArticleTitle('Other');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}