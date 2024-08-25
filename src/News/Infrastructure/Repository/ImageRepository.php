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
        $this->_em->persist($image);
        $this->_em->flush();

        return $image->getId();
    }

    public function findById(int $id): ?Image
    {
        return $this->find($id);
    }

    public function deleteById(int $imageID): void
    {
        $queryBuilder = $this->createQueryBuilder('i');
        $queryBuilder
            ->delete(Image::class, 'i')
            ->where('i.id = :id')
            ->setParameter('id', $imageID);

        $queryBuilder->getQuery()->execute();
    }
}