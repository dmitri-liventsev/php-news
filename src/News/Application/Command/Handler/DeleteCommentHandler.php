<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\DeleteCommentCommand;
use App\News\Domain\Repository\CommentRepositoryInterface;

class DeleteCommentHandler
{
    private CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function __invoke(DeleteCommentCommand $command): void
    {
        $this->commentRepository->deleteById($command->commentID);
    }
}