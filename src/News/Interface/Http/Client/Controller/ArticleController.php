<?php

namespace App\News\Interface\Http\Client\Controller;

use App\News\Application\Query\GetArticleByIdQuery;
use App\News\Application\Query\GetArticlesByCategoryQuery;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function getArticleById(int $article_id): JsonResponse
    {
        $articleId = new ArticleID($article_id);
        $article = $this->handle(new GetArticleByIdQuery($articleId));

        if (!$article) {
            return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($article);
    }

    public function getArticlesByCategory(int $category_id, int $page_id = 0): JsonResponse
    {
        $categoryID = new CategoryID($category_id);
        $query = new GetArticlesByCategoryQuery($categoryID, $page_id);
        $articles = $this->handle($query);

        if (empty($articles)) {
            return new JsonResponse(['error' => 'No articles found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($articles);
    }
}
