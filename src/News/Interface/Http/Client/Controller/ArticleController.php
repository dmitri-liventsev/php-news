<?php

namespace App\News\Interface\Http\Client\Controller;

use App\News\Application\Query\GetArticleByIdQuery;
use App\News\Application\Query\GetArticlesByCategoryQuery;
use App\News\Domain\Exception\ArticleNotFoundException;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\CategoryID;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

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
        $articleID = new ArticleID($article_id);
        $article = $this->handle(new GetArticleByIdQuery($articleID));

        if (!$article) {
            throw ArticleNotFoundException::byId($articleID);
        }

        return $this->json($article);
    }

    public function getArticlesByCategory(int $category_id, int $page_id = 1): JsonResponse
    {
        $categoryID = new CategoryID($category_id);
        $data = $this->handle(new GetArticlesByCategoryQuery($categoryID, $page_id));

        return $this->json($data);
    }
}
