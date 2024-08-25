<?php

namespace App\News\Infrastructure\Repository;

use App\News\Domain\Entity\Category;
use App\News\Domain\Entity\Comment;
use App\News\Domain\Repository\CommentRepositoryInterface;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CommentID;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository implements CommentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $comment): CommentID
    {
        $this->_em->persist($comment);
        $this->_em->flush();

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

    public function findByArticle(ArticleID $articleID): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->where('c.article = :articleID')
            ->setParameter('articleID', $articleID->getValue())
            ->getQuery()
            ->getResult();
    }
}