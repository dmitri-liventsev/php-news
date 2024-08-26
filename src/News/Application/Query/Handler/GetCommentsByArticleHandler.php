<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetCommentsByArticleQuery;
use App\News\Domain\Entity\Comment;
use App\News\Domain\Repository\CommentRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetCommentsByArticleHandler
{
    private CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function __invoke(GetCommentsByArticleQuery $query): array | Collection
    {
        return $this->commentRepository->findByArticle($query->articleID);
    }
}