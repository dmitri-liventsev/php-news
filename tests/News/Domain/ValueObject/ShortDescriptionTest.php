<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\ShortDescription;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ShortDescriptionTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $description = new ShortDescription('A brief summary.');

        $this->assertSame('A brief summary.', $description->value);
        $this->assertSame('A brief summary.', (string) $description);
    }

    public function testTrimsSurroundingWhitespace(): void
    {
        $description = new ShortDescription("  A brief summary.  ");

        $this->assertSame('A brief summary.', $description->value);
    }

    public function testRejectsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ShortDescription('');
    }

    public function testRejectsWhitespaceOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ShortDescription("\t\n   ");
    }

    public function testRejectsValueExceeding500Characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ShortDescription(str_repeat('a', 501));
    }

    public function testAcceptsExactly500Characters(): void
    {
        $value = str_repeat('a', 500);

        $description = new ShortDescription($value);

        $this->assertSame($value, $description->value);
    }

    public function testEqualsByValue(): void
    {
        $a = new ShortDescription('Same');
        $b = new ShortDescription('Same');
        $c = new ShortDescription('Other');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}