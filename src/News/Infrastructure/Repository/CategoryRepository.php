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
        $this->_em->persist($category);
        $this->_em->flush();

        return $category->getId();
    }

    public function deleteById(CategoryID $categoryID): void
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder
            ->delete(Category::class, 'c')
            ->where('c.id = :id')
            ->setParameter('id', $categoryID);

        $queryBuilder->getQuery()->execute();
    }

    /**
     * @return Category[]
     */
    public function findCategoriesWithTopArticles(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.articles', 'a')
            ->addSelect('a')
            ->where('a.isTop = :isTop')
            ->setParameter('isTop', true)
            ->getQuery()
            ->getResult();
    }
}