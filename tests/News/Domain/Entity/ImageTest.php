<?php

namespace App\Tests\News\Domain\Entity;

use App\News\Domain\Entity\Image;
use App\News\Domain\ValueObject\ImageFileName;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testCreateInitializes(): void
    {
        $image = Image::create(new ImageFileName('cover.jpg'));

        $this->assertSame('cover.jpg', $image->getFileName()->value);
        $this->assertNull($image->getDeletedAt());
        $this->assertSame($image->getCreatedAt(), $image->getUpdatedAt());
    }

    public function testSoftDeleteIsIdempotent(): void
    {
        $image = Image::create(new ImageFileName('cover.jpg'));

        $image->softDelete();
        $first = $image->getDeletedAt();
        $this->assertNotNull($first);

        usleep(2000);
        $image->softDelete();
        $this->assertSame($first, $image->getDeletedAt());
    }
}
