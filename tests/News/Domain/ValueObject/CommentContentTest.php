<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\CommentContent;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CommentContentTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $content = new CommentContent('Nice article!');

        $this->assertSame('Nice article!', $content->value);
        $this->assertSame('Nice article!', (string) $content);
    }

    public function testTrimsSurroundingWhitespace(): void
    {
        $content = new CommentContent("\n  Nice  \n");

        $this->assertSame('Nice', $content->value);
    }

    public function testRejectsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CommentContent('');
    }

    public function testRejectsWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CommentContent("\t\n   ");
    }

    public function testAcceptsLargeContent(): void
    {
        $value = str_repeat('a', 10_000);

        $content = new CommentContent($value);

        $this->assertSame($value, $content->value);
    }

    public function testEqualsByValue(): void
    {
        $a = new CommentContent('Same');
        $b = new CommentContent('Same');
        $c = new CommentContent('Other');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}