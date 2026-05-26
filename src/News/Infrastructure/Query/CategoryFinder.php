<?php

namespace App\News\Infrastructure\Query;

use App\News\Application\Query\Finder\CategoryFinderInterface;
use App\News\Application\Query\Handler\DTO\ArticleDTO;
use App\News\Application\Query\Handler\DTO\CategoryPreviewDTO;
use App\News\Application\Query\Handler\DTO\CategoryWithTopArticlesDTO;
use App\News\Domain\ValueObject\CategoryID;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

/**
 * Read-side category projector: plain DBAL, no ORM, no entity hydration.
 *
 * Soft-deleted rows are filtered manually with `deleted_at IS NULL`.
 */
final readonly class CategoryFinder implements CategoryFinderInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function findOneById(CategoryID $id): ?CategoryPreviewDTO
    {
        $row = $this->connection->createQueryBuilder()
            ->select('c.id, c.title')
            ->from('category', 'c')
            ->where('c.id = :id')
            ->andWhere('c.deleted_at IS NULL')
            ->setParameter('id', $id->value)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return new CategoryPreviewDTO((int) $row['id'], (string) $row['title']);
    }

    public function findAll(): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('c.id, c.title')
            ->from('category', 'c')
            ->where('c.deleted_at IS NULL')
            ->orderBy('c.id', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(
            fn($r) => new CategoryPreviewDTO((int) $r['id'], (string) $r['title']),
            $rows,
        );
    }

    public function findCategoriesWithTopArticles(): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(
                'c.id AS category_id',
                'c.title AS category_title',
                'a.id AS article_id',
                'a.title AS article_title',
                'a.short_description AS article_short_description',
                'a.content AS article_content',
                'a.number_of_views AS article_number_of_views',
                'a.is_top AS article_is_top',
                'a.created_at AS article_created_at',
                'a.updated_at AS article_updated_at',
                'a.deleted_at AS article_deleted_at',
                'i.id AS image_id',
                'i.file_name AS image_file_name',
            )
            ->from('category', 'c')
            ->innerJoin('c', 'article_category', 'ac', 'ac.category_id = c.id')
            ->innerJoin('ac', 'article', 'a', 'a.id = ac.article_id AND a.deleted_at IS NULL AND a.is_top = 1')
            ->leftJoin('a', 'image', 'i', 'i.id = a.image_id AND i.deleted_at IS NULL')
            ->where('c.deleted_at IS NULL')
            ->orderBy('c.id', 'ASC')
            ->addOrderBy('a.created_at', 'DESC')
            ->addOrderBy('a.id', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        if ($rows === []) {
            return [];
        }

        $articleCategories = $this->categoriesForArticles(
            array_values(array_unique(array_map(fn($r) => (int) $r['article_id'], $rows))),
        );

        $result = [];
        foreach ($rows as $row) {
            $catId = (int) $row['category_id'];
            $result[$catId] ??= new CategoryWithTopArticlesDTO(
                id: $catId,
                title: (string) $row['category_title'],
                articles: [],
            );

            $image = null;
            if ($row['image_id'] !== null) {
                $image = ['id' => (int) $row['image_id'], 'fileName' => (string) $row['image_file_name']];
            }

            $result[$catId]->articles[] = new ArticleDTO(
                id: (int) $row['article_id'],
                title: (string) $row['article_title'],
                shortDescription: (string) $row['article_short_description'],
                content: (string) $row['article_content'],
                image: $image,
                numberOfViews: (int) $row['article_number_of_views'],
                isTop: (bool) $row['article_is_top'],
                categories: $articleCategories[(int) $row['article_id']] ?? [],
                createdAt: self::formatTimestamp((string) $row['article_created_at']),
                updatedAt: self::formatTimestamp((string) $row['article_updated_at']),
                deletedAt: $row['article_deleted_at'] !== null ? self::formatTimestamp((string) $row['article_deleted_at']) : null,
            );
        }

        return array_values($result);
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
