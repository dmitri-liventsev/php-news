<?php

namespace App\News\Interface\Http\Admin\Controller;

use App\News\Application\Command\DeleteCategoryCommand;
use App\News\Application\Query\GetCategoriesQuery;
use App\News\Application\Query\GetCategoryByIdQuery;
use App\News\Domain\Exception\CategoryNotFoundException;
use App\News\Domain\ValueObject\CategoryID;
use App\News\Interface\Http\Admin\Controller\Request\CreateCategoryRequest;
use App\News\Interface\Http\Admin\Controller\Request\UpdateCategoryRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class AdminCategoryController extends AbstractController
{
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function getCategory(int $category_id): JsonResponse
    {
        $categoryID = new CategoryID($category_id);
        $category = $this->handle(new GetCategoryByIdQuery($categoryID));

        if (!$category) {
            throw CategoryNotFoundException::byId($categoryID);
        }

        return $this->json($category);
    }

    public function getCategories(): JsonResponse
    {
        $categories = $this->handle(new GetCategoriesQuery());

        return $this->json($categories);
    }

    public function createCategory(CreateCategoryRequest $request): JsonResponse
    {
        $categoryID = $this->handle($request->toCommand());

        return new JsonResponse(['status' => 'Category created', 'category_id' => $categoryID], Response::HTTP_CREATED);
    }

    public function updateCategory(int $category_id, UpdateCategoryRequest $request): JsonResponse
    {
        $this->handle($request->toCommand($category_id));

        return new JsonResponse(['status' => 'Category updated']);
    }

    public function deleteCategory(int $category_id): JsonResponse
    {
        $categoryID = new CategoryID($category_id);
        $this->handle(new DeleteCategoryCommand($categoryID));

        return new JsonResponse(['status' => 'Category deleted']);
    }
}

