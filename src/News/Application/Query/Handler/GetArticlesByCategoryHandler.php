<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\Finder\ArticleFinderInterface;
use App\News\Application\Query\Finder\CategoryFinderInterface;
use App\News\Application\Query\GetArticlesByCategoryQuery;
use App\News\Domain\Exception\CategoryNotFoundException;

class GetArticlesByCategoryHandler
{
    public function __construct(
        private readonly ArticleFinderInterface  $articleFinder,
        private readonly CategoryFinderInterface $categoryFinder,
    ) {
    }

    public function __invoke(GetArticlesByCategoryQuery $query): array
    {
        $category = $this->categoryFinder->findOneById($query->categoryID);
        if ($category === null) {
            throw CategoryNotFoundException::byId($query->categoryID);
        }

        $offset = ($query->page - 1) * $query->limit;
        $articles = $this->articleFinder->findByCategoryPage($query->categoryID, $query->limit, $offset);

        return [
            'articles' => $articles,
            'category' => $category,
        ];
    }
}
