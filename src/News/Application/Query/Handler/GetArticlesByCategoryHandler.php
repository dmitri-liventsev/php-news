<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetArticlesByCategoryQuery;
use App\News\Application\Query\Handler\DTO\ArticleDTO;
use App\News\Application\Query\Handler\DTO\CategoryPreviewDTO;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;

class GetArticlesByCategoryHandler
{
    private ArticleRepositoryInterface $articleRepository;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository, CategoryRepositoryInterface $categoryRepository) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(GetArticlesByCategoryQuery $query): array
    {
        $offset = ($query->page - 1) * $query->limit;

        $category = $this->categoryRepository->findById($query->categoryID);
        $articles = $this->articleRepository->findByCategoryWithPagination($query->categoryID, $query->limit, $offset);
        $articles = array_map(fn ($article) => new ArticleDTO($article), $articles);

        return [
            'articles' => $articles,
            'category' => new CategoryPreviewDTO($category)
        ];
    }
}