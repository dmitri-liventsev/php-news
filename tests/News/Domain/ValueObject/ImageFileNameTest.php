<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\ImageFileName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ImageFileNameTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $name = new ImageFileName('cover.jpg');

        $this->assertSame('cover.jpg', $name->value);
        $this->assertSame('cover.jpg', (string) $name);
    }

    public function testTrimsSurroundingWhitespace(): void
    {
        $name = new ImageFileName('  cover.jpg  ');

        $this->assertSame('cover.jpg', $name->value);
    }

    public function testRejectsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ImageFileName('');
    }

    public function testRejectsWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ImageFileName("\t\n   ");
    }

    public function testRejectsValueExceeding255Characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ImageFileName(str_repeat('a', 252) . '.jpg');
    }

    public function testAcceptsExactly255Characters(): void
    {
        $value = str_repeat('a', 251) . '.jpg';

        $name = new ImageFileName($value);

        $this->assertSame($value, $name->value);
    }

    public function testEqualsByValue(): void
    {
        $a = new ImageFileName('cover.jpg');
        $b = new ImageFileName('cover.jpg');
        $c = new ImageFileName('other.png');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}