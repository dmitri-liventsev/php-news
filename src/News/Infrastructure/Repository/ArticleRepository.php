<?php

namespace App\News\Infrastructure\Repository;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\User;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\Expr\Join;
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
        $article = $this->find($articleID->getValue());

        if (!$article) {
            return;
        }

        $this->getEntityManager()->remove($article);
        $this->getEntityManager()->flush();
    }

    /**
     * @param CategoryID $categoryID
     * @param int $limit
     * @param int $offset
     * @return Collection|Article[]
     */
    public function findByCategoryWithPagination(CategoryID $categoryID, int $limit, int $offset): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.categories', 'c')
            ->where('c.id = :categoryID')
            ->setParameter('categoryID', $categoryID->getValue())
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Collection|Article[]
     */
    public function findWithPagination(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('a')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function resetTopArticlesByCategory(CategoryID $categoryID): void
    {
        $currentTopArticles = $this->createQueryBuilder('a')
            ->innerJoin('a.categories', 'c')
            ->where('c.id = :categoryID')
            ->setParameter('categoryID', $categoryID->getValue())
            ->andWhere('a.isTop = :isTop')
            ->setParameter('isTop', true)
            ->getQuery()
            ->getResult();

        $articleIds = [];
        foreach ($currentTopArticles as $currentTopArticle) {
            $articleIds[] = $currentTopArticle->getId()->getValue();
        }
        if (empty($articleIds)) {
            return;
        }

        $this->getEntityManager()->createQueryBuilder()
            ->update(Article::class, 'a')
            ->set('a.isTop', ':isTop')
            ->where('a.id IN (:articleIds)')
            ->setParameter('articleIds', $articleIds)
            ->setParameter('isTop', false)
            ->getQuery()
            ->execute();
    }

    /**
     * @param CategoryID $categoryID
     * @param int $limit
     * @return Collection|Article[]
     */
    public function findTopArticlesByCategory(CategoryID $categoryID, int $limit = 3): array
    {
        return $this->createQueryBuilder('a')
            ->where(':categoryID MEMBER OF a.categories')
            ->setParameter('categoryID', $categoryID)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function refreshTopArticlesByCategory(CategoryID $categoryID, int $limit = 3): void
    {
        $topArticles = $this->findTopArticlesByCategory($categoryID, $limit);
        if (empty($topArticles)) {
            return;
        }
        $articleIds = [];
        foreach ($topArticles as $currentTopArticle) {
            $articleIds[] = $currentTopArticle->getId()->getValue();
        }

        $this->getEntityManager()->createQueryBuilder()
            ->update(Article::class, 'a')
            ->set('a.isTop', ':isTop')
            ->where('a.id IN (:articleIds)')
            ->setParameter('articleIds', $articleIds)
            ->setParameter('isTop', true)
            ->getQuery()
            ->execute();

        $this->getEntityManager()->createQueryBuilder()
            ->update(Article::class, 'a')
            ->set('a.isTop', ':isTop')
            ->where('a.id NOT IN (:articleIds)')
            ->setParameter('articleIds', $articleIds)
            ->setParameter('isTop', false)
            ->getQuery()
            ->execute();

    }

    public function increaseNumberOfView(ArticleID $articleID): void
    {
        /** @var Connection $connection */
        $connection = $this->getEntityManager()->getConnection();

        $sql = 'UPDATE article SET number_of_views = number_of_views + 1 WHERE id = :id';
        $statement = $connection->prepare($sql);
        $statement->bindValue('id', $articleID->getValue());
        $statement->executeStatement();
    }

    /**
     * @param DateTimeInterface $from
     * @param int $limit
     * @return Collection|Article[]
     */
    public function findTopArticles(DateTimeInterface $from, int $limit): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.createdAt >= :from')
            ->setParameter('from', $from)
            ->orderBy('a.numberOfViews', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findById(ArticleID $articleID)
    {
        return $this->find($articleID->getValue());
    }
}