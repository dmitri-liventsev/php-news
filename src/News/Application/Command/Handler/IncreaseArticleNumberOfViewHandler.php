<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\IncreaseArticleNumberOfViewCommand;
use App\News\Domain\Exception\ArticleNotFoundException;
use App\News\Domain\Repository\ArticleRepositoryInterface;

class IncreaseArticleNumberOfViewHandler
{
    public function __construct(private readonly ArticleRepositoryInterface $articleRepository)
    {
    }

    public function __invoke(IncreaseArticleNumberOfViewCommand $command): void
    {
        $article = $this->articleRepository->findById($command->articleId);
        if (!$article) {
            throw ArticleNotFoundException::byId($command->articleId);
        }

        $article->incrementViews();

        $this->articleRepository->save($article);
    }
}
