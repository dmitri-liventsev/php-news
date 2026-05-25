<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\DeleteCommentCommand;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CommentRepositoryInterface;

class DeleteCommentHandler
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly ArticleRepositoryInterface $articleRepository,
    ) {
    }

    public function __invoke(DeleteCommentCommand $command): void
    {
        $comment = $this->commentRepository->findById($command->commentID);
        if ($comment === null) {
            return;
        }

        $article = $comment->getArticle();
        $article->removeComment($command->commentID);

        $this->articleRepository->save($article);
    }
}
