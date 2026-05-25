<?php

namespace App\News\Domain\Repository;

use App\News\Domain\Entity\Image;
use App\News\Domain\ValueObject\ImageID;

interface ImageRepositoryInterface
{
    public function save(Image $image): ImageID;

    public function findById(ImageID $imageID): ?Image;

    public function deleteById(ImageID $imageID): void;
}
