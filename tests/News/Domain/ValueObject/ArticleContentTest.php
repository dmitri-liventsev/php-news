<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\ArticleContent;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ArticleContentTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $content = new ArticleContent('Long-form body of the article.');

        $this->assertSame('Long-form body of the article.', $content->value);
        $this->assertSame('Long-form body of the article.', (string) $content);
    }

    public function testTrimsSurroundingWhitespace(): void
    {
        $content = new ArticleContent("\n  Body  \n");

        $this->assertSame('Body', $content->value);
    }

    public function testRejectsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ArticleContent('');
    }

    public function testRejectsWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ArticleContent("\t\n   ");
    }

    public function testAcceptsLargeContent(): void
    {
        $value = str_repeat('a', 100_000);

        $content = new ArticleContent($value);

        $this->assertSame($value, $content->value);
    }

    public function testEqualsByValue(): void
    {
        $a = new ArticleContent('Same');
        $b = new ArticleContent('Same');
        $c = new ArticleContent('Other');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}