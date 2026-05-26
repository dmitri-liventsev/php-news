<?php

namespace App\News\Infrastructure\Query;

use App\News\Application\Query\Finder\ArticleFinderInterface;
use App\News\Application\Query\Handler\DTO\ArticleDTO;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;

/**
 * Read-side article projector: plain DBAL, no ORM, no entity hydration.
 *
 * Each query hits `article` (and optionally joins `image`, `article_category`),
 * then a follow-up call to {@see categoriesForArticles()} loads the m2m
 * categories for the returned ids in a single batched query.
 *
 * Soft-deleted rows are filtered by hand (`deleted_at IS NULL`) — the ORM
 * SoftDeleteFilter does NOT apply to raw DBAL queries.
 */
final readonly class ArticleFinder implements ArticleFinderInterface
{
    private const ARTICLE_COLUMNS = 'a.id, a.title, a.short_description, a.content, '
        . 'a.number_of_views, a.is_top, a.created_at, a.updated_at, a.deleted_at, '
        . 'i.id AS image_id, i.file_name AS image_file_name';

    public function __construct(private Connection $connection)
    {
    }

    public function findOneById(ArticleID $id): ?ArticleDTO
    {
        $row = $this->connection->createQueryBuilder()
            ->select(self::ARTICLE_COLUMNS)
            ->from('article', 'a')
            ->leftJoin('a', 'image', 'i', 'i.id = a.image_id AND i.deleted_at IS NULL')
            ->where('a.id = :id')
            ->andWhere('a.deleted_at IS NULL')
            ->setParameter('id', $id->value)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            return null;
        }

        $categoriesByArticle = $this->categoriesForArticles([(int) $row['id']]);

        return $this->hydrate($row, $categoriesByArticle[(int) $row['id']] ?? []);
    }

    public function findRecentPage(int $limit, int $offset): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(self::ARTICLE_COLUMNS)
            ->from('article', 'a')
            ->leftJoin('a', 'image', 'i', 'i.id = a.image_id AND i.deleted_at IS NULL')
            ->where('a.deleted_at IS NULL')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->hydrateMany($rows);
    }

    public function findByCategoryPage(CategoryID $categoryID, int $limit, int $offset): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(self::ARTICLE_COLUMNS)
            ->from('article', 'a')
            ->leftJoin('a', 'image', 'i', 'i.id = a.image_id AND i.deleted_at IS NULL')
            ->innerJoin('a', 'article_category', 'ac', 'ac.article_id = a.id')
            ->where('ac.category_id = :categoryID')
            ->andWhere('a.deleted_at IS NULL')
            ->orderBy('a.created_at', 'DESC')
            ->addOrderBy('a.id', 'DESC')
            ->setParameter('categoryID', $categoryID->value)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->hydrateMany($rows);
    }

    public function findTopSince(DateTimeInterface $since, int $limit): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(self::ARTICLE_COLUMNS)
            ->from('article', 'a')
            ->leftJoin('a', 'image', 'i', 'i.id = a.image_id AND i.deleted_at IS NULL')
            ->where('a.created_at >= :since')
            ->andWhere('a.deleted_at IS NULL')
            ->orderBy('a.number_of_views', 'DESC')
            ->setParameter('since', $since->format('Y-m-d H:i:s'))
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->hydrateMany($rows);
    }

    /**
     * @param array<int, mixed> $rows
     * @return ArticleDTO[]
     */
    private function hydrateMany(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $ids = array_map(fn($r) => (int) $r['id'], $rows);
        $categoriesByArticle = $this->categoriesForArticles($ids);

        return array_map(
            fn($row) => $this->hydrate($row, $categoriesByArticle[(int) $row['id']] ?? []),
            $rows,
        );
    }

    /**
     * @param array<string, mixed>                   $row
     * @param list<array{id: int, title: string}>    $categories
     */
    private function hydrate(array $row, array $categories): ArticleDTO
    {
        $image = null;
        if ($row['image_id'] !== null) {
            $image = ['id' => (int) $row['image_id'], 'fileName' => (string) $row['image_file_name']];
        }

        return new ArticleDTO(
            id: (int) $row['id'],
            title: (string) $row['title'],
            shortDescription: (string) $row['short_description'],
            content: (string) $row['content'],
            image: $image,
            numberOfViews: (int) $row['number_of_views'],
            isTop: (bool) $row['is_top'],
            categories: $categories,
            createdAt: self::formatTimestamp((string) $row['created_at']),
            updatedAt: self::formatTimestamp((string) $row['updated_at']),
            deletedAt: $row['deleted_at'] !== null ? self::formatTimestamp((string) $row['deleted_at']) : null,
        );
    }

    /**
     * @param int[] $articleIDs
     * @return array<int, list<array{id: int, title: string}>>
     */
    private function categoriesForArticles(array $articleIDs): array
    {
        if ($articleIDs === []) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('ac.article_id, c.id AS cat_id, c.title AS cat_title')
            ->from('article_category', 'ac')
            ->innerJoin('ac', 'category', 'c', 'c.id = ac.category_id AND c.deleted_at IS NULL')
            ->where('ac.article_id IN (:ids)')
            ->setParameter('ids', $articleIDs, Connection::PARAM_INT_ARRAY)
            ->orderBy('c.id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = [];
        foreach ($rows as $row) {
            $aid = (int) $row['article_id'];
            $result[$aid] ??= [];
            $result[$aid][] = ['id' => (int) $row['cat_id'], 'title' => (string) $row['cat_title']];
        }
        return $result;
    }

    private static function formatTimestamp(string $sqlDateTime): string
    {
        return (new DateTimeImmutable($sqlDateTime))->format('c');
    }
}
