<?php

namespace App\News\Infrastructure\Repository;

use App\News\Domain\Entity\Comment;
use App\News\Domain\Repository\CommentRepositoryInterface;
use App\News\Domain\ValueObject\CommentID;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository implements CommentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findById(CommentID $commentID): ?Comment
    {
        return $this->find($commentID->value);
    }
}
