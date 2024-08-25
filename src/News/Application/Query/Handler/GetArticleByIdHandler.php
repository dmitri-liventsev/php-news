<?php

namespace App\News\Application\Query\Handler;

use App\News\Application\Query\GetArticleByIdQuery;
use App\News\Application\Query\Handler\DTO\ArticleDTO;
use App\News\Domain\Entity\Article;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetArticleByIdHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository) {
        $this->articleRepository = $articleRepository;
    }

    public function __invoke(GetArticleByIdQuery $query): ArticleDTO
    {
        $article = $this->articleRepository->find($query->articleID->getValue());

        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }

        return new ArticleDTO($article);
    }
}