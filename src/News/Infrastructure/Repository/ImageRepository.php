<?php

namespace App\News\Infrastructure\Repository;

use App\News\Domain\Entity\Image;
use App\News\Domain\Repository\ImageRepositoryInterface;
use App\News\Domain\ValueObject\ImageID;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ImageRepository extends ServiceEntityRepository implements ImageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function save(Image $image): ImageID
    {
        $this->getEntityManager()->persist($image);
        $this->getEntityManager()->flush();

        return $image->getId();
    }

    public function findById(int $id): ?Image
    {
        return $this->find($id);
    }

    public function deleteById(ImageID $imageID): void
    {

        $image = $this->find($imageID->getValue());

        if (!$image) {
            return;
        }

        $this->getEntityManager()->remove($image);
        $this->getEntityManager()->flush();
    }
}