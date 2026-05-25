<?php

namespace App\News\Infrastructure\Query;

use App\News\Application\Query\Finder\CategoryFinderInterface;
use App\News\Application\Query\Handler\DTO\CategoryPreviewDTO;
use App\News\Domain\Entity\Category;
use App\News\Domain\ValueObject\CategoryID;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CategoryFinder implements CategoryFinderInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findOneById(CategoryID $id): ?CategoryPreviewDTO
    {
        $category = $this->em->find(Category::class, $id->value);

        return $category ? new CategoryPreviewDTO($category) : null;
    }

    public function findAll(): array
    {
        $categories = $this->em->createQueryBuilder()
            ->select('c')
            ->from(Category::class, 'c')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(fn(Category $c) => new CategoryPreviewDTO($c), $categories);
    }

    public function findCategoriesWithTopArticles(): array
    {
        return $this->em->createQueryBuilder()
            ->select('c', 'a', 'i')
            ->from(Category::class, 'c')
            ->leftJoin('c.articles', 'a')
            ->leftJoin('a.image', 'i')
            ->where('a.isTop = :isTop')
            ->setParameter('isTop', true)
            ->orderBy('c.id', 'ASC')
            ->addOrderBy('a.createdAt', 'DESC')
            ->addOrderBy('a.id', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
