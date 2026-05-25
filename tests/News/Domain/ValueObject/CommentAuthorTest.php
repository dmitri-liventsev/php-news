<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\CommentAuthor;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CommentAuthorTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $author = new CommentAuthor('John Doe');

        $this->assertSame('John Doe', $author->value);
        $this->assertSame('John Doe', (string) $author);
    }

    public function testTrimsSurroundingWhitespace(): void
    {
        $author = new CommentAuthor('  John Doe  ');

        $this->assertSame('John Doe', $author->value);
    }

    public function testRejectsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CommentAuthor('');
    }

    public function testRejectsWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CommentAuthor("\t\n   ");
    }

    public function testRejectsValueExceeding255Characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CommentAuthor(str_repeat('a', 256));
    }

    public function testAcceptsExactly255Characters(): void
    {
        $value = str_repeat('a', 255);

        $author = new CommentAuthor($value);

        $this->assertSame($value, $author->value);
    }

    public function testEqualsByValue(): void
    {
        $a = new CommentAuthor('Same');
        $b = new CommentAuthor('Same');
        $c = new CommentAuthor('Other');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}