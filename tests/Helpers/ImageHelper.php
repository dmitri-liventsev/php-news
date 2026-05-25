<?php

namespace App\Tests\Helpers;

use App\News\Domain\Entity\Image;
use App\News\Domain\ValueObject\ImageFileName;

class ImageHelper
{
    public static function buildImage(): Image
    {
        return Image::create(new ImageFileName('file_name.jpg'));
    }
}