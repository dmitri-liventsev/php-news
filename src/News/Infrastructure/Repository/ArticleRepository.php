<?php

namespace App\News\Infrastructure\Repository;

use App\News\Domain\Entity\Article;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository implements ArticleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $article): ArticleID
    {
        $this->getEntityManager()->persist($article);
        $this->getEntityManager()->flush();

        return $article->getId();
    }

    public function deleteById(ArticleID $articleID): void
    {
        $article = $this->find($articleID->value);

        if (!$article) {
            return;
        }

        $this->getEntityManager()->remove($article);
        $this->getEntityManager()->flush();
    }

    public function findById(ArticleID $articleID): ?Article
    {
        return $this->find($articleID->value);
    }

    public function findLatestByCategory(CategoryID $categoryID, int $limit): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.categories', 'c')
            ->where('c.id = :categoryID')
            ->setParameter('categoryID', $categoryID->value)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findCurrentTopByCategory(CategoryID $categoryID): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.categories', 'c')
            ->where('c.id = :categoryID')
            ->andWhere('a.isTop = :isTop')
            ->setParameter('categoryID', $categoryID->value)
            ->setParameter('isTop', true)
            ->getQuery()
            ->getResult();
    }
}
