<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetTopNewsQuery;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;

class GetTopNewsHandler
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository, ArticleRepositoryInterface $articleRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(GetTopNewsQuery $query): array
    {
        $categoriesWithArticles = $this->categoryRepository->findCategoriesWithTopArticles(3);

        $result = [];

        foreach ($categoriesWithArticles as $categoryId => $data) {
            $category = $this->categoryRepository->find($categoryId);
            if ($category) {
                $result[] = [
                    'category' => $category,
                    'articles' => $data['articles']
                ];
            }
        }

        return $result;
    }
}