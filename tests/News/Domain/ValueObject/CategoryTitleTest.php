<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\CategoryTitle;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CategoryTitleTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $title = new CategoryTitle('Sports');

        $this->assertSame('Sports', $title->value);
        $this->assertSame('Sports', (string) $title);
    }

    public function testTrimsSurroundingWhitespace(): void
    {
        $title = new CategoryTitle('  Sports  ');

        $this->assertSame('Sports', $title->value);
    }

    public function testRejectsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CategoryTitle('');
    }

    public function testRejectsWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CategoryTitle("\t\n   ");
    }

    public function testRejectsValueExceeding255Characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CategoryTitle(str_repeat('a', 256));
    }

    public function testAcceptsExactly255Characters(): void
    {
        $value = str_repeat('a', 255);

        $title = new CategoryTitle($value);

        $this->assertSame($value, $title->value);
    }

    public function testEqualsByValue(): void
    {
        $a = new CategoryTitle('Same');
        $b = new CategoryTitle('Same');
        $c = new CategoryTitle('Other');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}