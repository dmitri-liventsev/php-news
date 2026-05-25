<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\ArticleID;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ArticleIDTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $id = new ArticleID(42);

        $this->assertSame(42, $id->value);
        $this->assertSame('42', (string) $id);
    }

    public function testRejectsZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ArticleID(0);
    }

    public function testRejectsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ArticleID(-1);
    }

    public function testEqualsByValue(): void
    {
        $a = new ArticleID(1);
        $b = new ArticleID(1);
        $c = new ArticleID(2);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}