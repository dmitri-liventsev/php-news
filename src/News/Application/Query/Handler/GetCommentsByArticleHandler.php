<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetCommentsByArticleQuery;
use App\News\Application\Query\Handler\DTO\CommentDTO;
use App\News\Domain\Entity\Comment;
use App\News\Domain\Repository\CommentRepositoryInterface;

class GetCommentsByArticleHandler
{
    private CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function __invoke(GetCommentsByArticleQuery $query): array
    {
        $comments = $this->commentRepository->findByArticle($query->articleID);

        return array_map(function (Comment $comment) {
            return new CommentDTO($comment);
        }, $comments);
    }
}