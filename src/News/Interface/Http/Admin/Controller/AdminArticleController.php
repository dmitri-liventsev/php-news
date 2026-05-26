<?php

namespace App\News\Interface\Http\Admin\Controller;

use App\News\Application\Command\DeleteArticleCommand;
use App\News\Application\Query\GetArticleByIdQuery;
use App\News\Application\Query\GetArticlesQuery;
use App\News\Domain\Entity\Image;
use App\News\Domain\Exception\ArticleNotFoundException;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Interface\Http\Admin\Controller\Request\CreateArticleRequest;
use App\News\Interface\Http\Admin\Controller\Request\CreateImageRequest;
use App\News\Interface\Http\Admin\Controller\Request\UpdateArticleRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

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
        $articleID = new ArticleID($article_id);
        $article = $this->handle(new GetArticleByIdQuery($articleID));

        if (!$article) {
            throw ArticleNotFoundException::byId($articleID);
        }

        return $this->json($article);
    }

    public function getArticles(int $page_id = 0): JsonResponse
    {
        $articles = $this->handle(new GetArticlesQuery($page_id));

        return $this->json($articles);
    }

    public function createArticle(CreateArticleRequest $request): JsonResponse
    {
        $articleID = $this->handle($request->toCommand());

        return new JsonResponse(['status' => 'Article created', 'article_id' => $articleID->value], Response::HTTP_CREATED);
    }

    public function updateArticle(int $article_id, UpdateArticleRequest $request): JsonResponse
    {
        $this->handle($request->toCommand($article_id));

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
        $image = $this->handle($request->toCommand());

        return new JsonResponse(['id' => $image->getId()->value, 'file_name' => $image->getFileName()->value], Response::HTTP_OK);
    }
}
