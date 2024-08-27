<?php

namespace App\News\Interface\Http\Admin\Controller;

use App\News\Application\Command\CreateCategoryCommand;
use App\News\Application\Command\DeleteCategoryCommand;
use App\News\Application\Command\UpdateCategoryCommand;
use App\News\Application\Query\GetCategoriesQuery;
use App\News\Application\Query\GetCategoryByIdQuery;
use App\News\Domain\ValueObject\CategoryID;
use App\News\Interface\Http\Admin\Controller\Request\CreateCategoryRequest;
use App\News\Interface\Http\Admin\Controller\Request\UpdateCategoryRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

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
            return new JsonResponse(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
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
        $categoryID = $this->handle(
            CreateCategoryCommand::fromRequest($request)
        );

        return new JsonResponse(['status' => 'Category created', 'category_id' => $categoryID], Response::HTTP_CREATED);
    }

    public function updateCategory(int $category_id, UpdateCategoryRequest $request): JsonResponse
    {
        $command = UpdateCategoryCommand::fromRequest($category_id, $request);
        $this->handle($command);

        return new JsonResponse(['status' => 'Category updated']);
    }

    public function deleteCategory(int $category_id): JsonResponse
    {
        $categoryID = new CategoryID($category_id);
        $this->handle(new DeleteCategoryCommand($categoryID));

        return new JsonResponse(['status' => 'Category deleted']);
    }
}

