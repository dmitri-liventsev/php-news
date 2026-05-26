<?php

namespace App\News\Interface\Http\Admin\Controller;

use App\News\Application\Command\DeleteCommentCommand;
use App\News\Application\Query\GetCommentsByArticleQuery;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CommentID;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class AdminCommentController extends AbstractController
{
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function getCommentsByArticle(int $article_id): JsonResponse
    {
        $articleID = new ArticleID($article_id);
        $comments = $this->handle(new GetCommentsByArticleQuery($articleID));

        return $this->json($comments);
    }

    public function deleteComment(int $comment_id): JsonResponse
    {
        $commentID = new CommentID($comment_id);
        $this->handle(new DeleteCommentCommand($commentID));

        return new JsonResponse(['status' => 'Comment deleted']);
    }
}
