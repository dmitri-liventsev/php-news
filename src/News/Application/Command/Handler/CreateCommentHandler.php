<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\CreateCommentCommand;
use App\News\Domain\Exception\ArticleNotFoundException;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\ValueObject\CommentAuthor;
use App\News\Domain\ValueObject\CommentContent;
use App\News\Domain\ValueObject\CommentID;

class CreateCommentHandler
{
    public function __construct(private readonly ArticleRepositoryInterface $articleRepository)
    {
    }

    public function __invoke(CreateCommentCommand $command): CommentID
    {
        $article = $this->articleRepository->findById($command->articleID);
        if (!$article) {
            throw ArticleNotFoundException::byId($command->articleID);
        }

        $comment = $article->addComment(
            new CommentAuthor($command->author),
            new CommentContent($command->content),
        );

        $this->articleRepository->save($article);

        return $comment->getId();
    }
}
