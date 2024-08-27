<?php

namespace App\News\Interface\Http\Admin\Controller;

use App\News\Application\Command\CreateArticleCommand;
use App\News\Application\Command\CreateImageCommand;
use App\News\Application\Command\DeleteArticleCommand;
use App\News\Application\Command\UpdateArticleCommand;
use App\News\Application\Query\GetArticleByIdQuery;
use App\News\Application\Query\GetArticlesQuery;
use App\News\Domain\Entity\Image;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Interface\Http\Admin\Controller\Request\CreateArticleRequest;
use App\News\Interface\Http\Admin\Controller\Request\CreateImageRequest;
use App\News\Interface\Http\Admin\Controller\Request\UpdateArticleRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleController extends AbstractController
{
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function getArticle(int $article_id): JsonResponse
    {
        $articleId = new ArticleID($article_id);
        $article = $this->handle(new GetArticleByIdQuery($articleId));

        if (!$article) {
            return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($article);
    }

    public function getArticles(int $page_id = 0): JsonResponse
    {
        $query = new GetArticlesQuery($page_id);
        $articles = $this->handle($query);

        if (empty($articles)) {
            return new JsonResponse(['error' => 'No articles found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($articles);
    }

    public function createArticle(CreateArticleRequest $request): JsonResponse
    {
        $articleId = $this->handle(
            CreateArticleCommand::fromRequest($request)
        );

        return new JsonResponse(['status' => 'Article created', 'article_id' => $articleId], Response::HTTP_CREATED);
    }

    public function updateArticle(int $article_id, UpdateArticleRequest $request): JsonResponse
    {
        $command = UpdateArticleCommand::fromRequest($article_id, $request);
        $this->handle($command);

        return new JsonResponse(['status' => 'Article updated']);
    }

    public function deleteArticle(int $article_id): JsonResponse
    {
        $articleID = new ArticleID($article_id);
        $this->handle(new DeleteArticleCommand($articleID));

        return new JsonResponse(['status' => 'Article deleted']);
    }

    public function uploadImage(CreateImageRequest $request): JsonResponse
    {
        /** @var Image $image */
        $image = $this->handle(CreateImageCommand::fromRequest(
            $request)
        );

        return new JsonResponse(['id' => $image->getId()->getValue(), 'file_name' => $image->getFileName()], Response::HTTP_OK);
    }
}
