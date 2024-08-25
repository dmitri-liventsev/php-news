<?php

namespace App\News\Interface\Http\Admin\Controller;

use App\News\Application\Command\DeleteCommentCommand;
use App\News\Application\Query\GetCommentsByArticleQuery;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CommentID;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    use HandleTrait;

    #[Route('/admin/article/{article_id}/comments', name: 'get_comments_by_article', methods: ['GET'])]
    public function getCommentsByArticle(int $article_id): JsonResponse
    {
        $articleID = new ArticleID($article_id);
        $comments = $this->handle(new GetCommentsByArticleQuery($articleID));

        if (empty($comments)) {
            return new JsonResponse(['message' => 'No comments found for this article'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($comments);
    }

    #[Route('/admin/comment/{comment_id}', name: 'delete_comment', methods: ['DELETE'])]
    public function deleteComment(int $comment_id, DeleteCommentCommand $command): JsonResponse
    {
        $commentID = new CommentID($comment_id);
        $this->handle(new DeleteCommentCommand($commentID));

        return new JsonResponse(['status' => 'Comment deleted']);
    }
}
