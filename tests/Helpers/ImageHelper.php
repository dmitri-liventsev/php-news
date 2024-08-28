<?php

namespace App\Tests\Helpers;

use App\News\Domain\Entity\Image;

class ImageHelper
{
    public static function buildImage(): Image {
        $image = new Image();
        $image->setFileName('file_name.jpg')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $image;
    }
}