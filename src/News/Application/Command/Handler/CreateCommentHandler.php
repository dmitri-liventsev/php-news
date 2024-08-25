<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\CreateCommentCommand;
use App\News\Domain\Entity\Comment;
use App\News\Domain\Repository\CommentRepositoryInterface;
use App\News\Domain\ValueObject\CommentID;

class CreateCommentHandler
{
    private CommentRepositoryInterface $commentRepository;

    /**
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function __invoke(CreateCommentCommand $command): CommentID
    {
        $comment = $this->buildComment($command);
        return $this->commentRepository->save($comment);
    }

    private function buildComment(CreateCommentCommand $command): Comment {
        $comment = new Comment();
        $comment->setAuthor($command->author)
            ->setContent($command->content)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $comment;
    }
}