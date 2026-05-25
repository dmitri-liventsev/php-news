<?php

namespace App\News\Infrastructure\Query;

use App\News\Application\Query\Finder\CommentFinderInterface;
use App\News\Application\Query\Handler\DTO\CommentDTO;
use App\News\Domain\Entity\Comment;
use App\News\Domain\ValueObject\ArticleID;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CommentFinder implements CommentFinderInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findByArticle(ArticleID $articleID): array
    {
        $comments = $this->em->createQueryBuilder()
            ->select('c')
            ->from(Comment::class, 'c')
            ->where('c.article = :articleID')
            ->setParameter('articleID', $articleID->value)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();

        return array_map(fn(Comment $c) => new CommentDTO($c), $comments);
    }
}
