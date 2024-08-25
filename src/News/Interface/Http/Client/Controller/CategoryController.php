<?php

namespace App\News\Interface\Http\Client\Controller;

use App\News\Application\Query\GetTopNewsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    use HandleTrait;

    public function getTopNews(): JsonResponse
    {
        $topNews = $this->handle(new GetTopNewsQuery());

        return $this->json($topNews);
    }
}
