<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\Finder\ArticleFinderInterface;
use App\News\Application\Query\GetArticlesByCategoryQuery;
use App\News\Application\Query\Handler\DTO\CategoryPreviewDTO;
use App\News\Domain\Repository\CategoryRepositoryInterface;

class GetArticlesByCategoryHandler
{
    public function __construct(
        private readonly ArticleFinderInterface      $articleFinder,
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    public function __invoke(GetArticlesByCategoryQuery $query): array
    {
        $offset = ($query->page - 1) * $query->limit;

        $category = $this->categoryRepository->findById($query->categoryID);
        $articles = $this->articleFinder->findByCategoryPage($query->categoryID, $query->limit, $offset);

        return [
            'articles' => $articles,
            'category' => new CategoryPreviewDTO($category),
        ];
    }
}
