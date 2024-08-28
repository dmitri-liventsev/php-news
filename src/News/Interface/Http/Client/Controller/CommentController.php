<?php

namespace App\News\Interface\Http\Client\Controller;

use App\News\Application\Command\CreateCommentCommand;
use App\News\Application\Query\GetCommentsByArticleQuery;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Interface\Http\Client\Controller\Request\CreateCommentRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class CommentController extends AbstractController
{
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function getComments(int $article_id): JsonResponse
    {
        $articleID = new ArticleID($article_id);
        $comments = $this->handle(new GetCommentsByArticleQuery($articleID));

        return $this->json($comments);
    }

    public function postComment(int $article_id, CreateCommentRequest $request): JsonResponse
    {
        $commentID = $this->handle(
            CreateCommentCommand::fromRequest($article_id, $request)
        );

        return new JsonResponse(['status' => 'Comment added successfully', 'ok' => true, 'id' => $commentID->getValue()], Response::HTTP_CREATED);
    }
}
