<?php

namespace App\News\Infrastructure\Repository;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Category;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\ValueObject\CategoryID;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository implements CategoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findById(CategoryID $categoryID): ?Category {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $categoryID->getValue())
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array $ids
     * @return Category[]
     */
    public function findByIds(array $ids): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    public function save(Category $category): CategoryID
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();

        return $category->getId();
    }

    public function deleteById(CategoryID $categoryID): void
    {
        $category = $this->findById($categoryID);
        $this->getEntityManager()->remove($category);
        $this->getEntityManager()->flush();
    }

    /**
     * @return Category[]
     */
    public function findCategoriesWithTopArticles(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.articles', 'a')
            ->leftJoin('a.image', 'i')
            ->addSelect('a')
            ->addSelect('i')
            ->where('a.isTop = :isTop')
            ->setParameter('isTop', true)
            ->getQuery()
            ->getArrayResult();
    }
}