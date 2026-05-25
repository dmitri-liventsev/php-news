<?php

namespace App\News\Infrastructure\Query;

use App\News\Application\Query\Finder\ArticleFinderInterface;
use App\News\Application\Query\Handler\DTO\ArticleDTO;
use App\News\Domain\Entity\Article;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Read-side: hydrates aggregates through the ORM and wraps them into flat DTOs.
 * Can later be rewritten on top of plain DBAL without hydration, keeping the interface intact.
 */
final readonly class ArticleFinder implements ArticleFinderInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findOneById(ArticleID $id): ?ArticleDTO
    {
        $article = $this->em->find(Article::class, $id->value);

        return $article ? new ArticleDTO($article) : null;
    }

    public function findRecentPage(int $limit, int $offset): array
    {
        $articles = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Article::class, 'a')
            ->orderBy('a.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return array_map(fn(Article $a) => new ArticleDTO($a), $articles);
    }

    public function findByCategoryPage(CategoryID $categoryID, int $limit, int $offset): array
    {
        $articles = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Article::class, 'a')
            ->innerJoin('a.categories', 'c')
            ->where('c.id = :categoryID')
            ->setParameter('categoryID', $categoryID->value)
            ->orderBy('a.createdAt', 'DESC')
            ->addOrderBy('a.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return array_map(fn(Article $a) => new ArticleDTO($a), $articles);
    }

    public function findTopSince(DateTimeInterface $since, int $limit): array
    {
        $articles = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Article::class, 'a')
            ->where('a.createdAt >= :since')
            ->setParameter('since', $since)
            ->orderBy('a.numberOfViews', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return array_map(fn(Article $a) => new ArticleDTO($a), $articles);
    }
}
