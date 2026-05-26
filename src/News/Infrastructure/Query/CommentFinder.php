<?php

namespace App\News\Infrastructure\Query;

use App\News\Application\Query\Finder\CommentFinderInterface;
use App\News\Application\Query\Handler\DTO\CommentDTO;
use App\News\Domain\ValueObject\ArticleID;
use Doctrine\DBAL\Connection;

/**
 * Read-side comment projector: plain DBAL, no ORM, no entity hydration.
 */
final readonly class CommentFinder implements CommentFinderInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function findByArticle(ArticleID $articleID): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('c.id, c.author, c.content')
            ->from('comment', 'c')
            ->where('c.article_id = :articleID')
            ->andWhere('c.deleted_at IS NULL')
            ->setParameter('articleID', $articleID->value)
            ->orderBy('c.id', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(
            fn($r) => new CommentDTO((int) $r['id'], (string) $r['author'], (string) $r['content']),
            $rows,
        );
    }
}
