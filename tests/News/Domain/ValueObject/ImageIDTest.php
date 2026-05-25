<?php

namespace App\Tests\News\Domain\ValueObject;

use App\News\Domain\ValueObject\ImageID;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ImageIDTest extends TestCase
{
    public function testHoldsValue(): void
    {
        $id = new ImageID(13);

        $this->assertSame(13, $id->value);
        $this->assertSame('13', (string) $id);
    }

    public function testRejectsZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ImageID(0);
    }

    public function testRejectsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ImageID(-7);
    }

    public function testEqualsByValue(): void
    {
        $a = new ImageID(1);
        $b = new ImageID(1);
        $c = new ImageID(2);

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}