<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\CommentID;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CommentIDTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $id = new CommentID(99);

        $this->assertSame(99, $id->value);
        $this->assertSame('99', (string) $id);
    }

    public function testRejectsZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CommentID(0);
    }

    public function testRejectsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CommentID(-3);
    }

    public function testEqualsByValue(): void
    {
        $a = new CommentID(1);
        $b = new CommentID(1);
        $c = new CommentID(2);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}