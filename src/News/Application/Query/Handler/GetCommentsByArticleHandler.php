<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetCommentsByArticleQuery;
use App\News\Domain\Entity\Comment;
use App\News\Domain\Repository\CommentRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetCommentsByArticleHandler
{
    private CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function __invoke(GetCommentsByArticleQuery $query): Comment
    {
        $comment = $this->commentRepository->findByArticle($query->articleID);

        if (!$comment) {
            throw new NotFoundHttpException('Comment not found');
        }

        return $comment;
    }
}