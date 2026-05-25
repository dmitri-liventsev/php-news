<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\CategoryID;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CategoryIDTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $id = new CategoryID(7);

        $this->assertSame(7, $id->value);
        $this->assertSame('7', (string) $id);
    }

    public function testRejectsZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CategoryID(0);
    }

    public function testRejectsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CategoryID(-5);
    }

    public function testEqualsByValue(): void
    {
        $a = new CategoryID(1);
        $b = new CategoryID(1);
        $c = new CategoryID(2);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}