<?php

namespace App\News\Infrastructure\Repository;

use App\News\Domain\Entity\Comment;
use App\News\Domain\Repository\CommentRepositoryInterface;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CommentID;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Collection;

class CommentRepository extends ServiceEntityRepository implements CommentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $comment): CommentID
    {
        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->flush();

        return $comment->getId();
    }

    public function deleteById(CommentID $commentID): void
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder
            ->delete(Comment::class, 'c')
            ->where('c.id = :id')
            ->setParameter('id', $commentID);

        $queryBuilder->getQuery()->execute();
    }

    public function findByArticle(ArticleID $articleID): array | Collection
    {
        return $this->createQueryBuilder('c')
            ->where('c.article = :articleID')
            ->setParameter('articleID', $articleID->getValue())
            ->getQuery()
            ->getResult();
    }
}