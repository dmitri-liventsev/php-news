<?php

namespace App\News\Domain\Repository;

use App\News\Domain\Entity\Image;
use App\News\Domain\Validator\Constraints\ImageID;

interface ImageRepositoryInterface
{
    public function save(Image $image): ImageID;

    public function findById(int $id): ?Image;

    public function deleteById(int $imageID): void;
}