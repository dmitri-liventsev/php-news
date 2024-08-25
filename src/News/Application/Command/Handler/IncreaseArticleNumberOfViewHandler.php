<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\IncreaseArticleNumberOfViewCommand;
use App\News\Domain\Repository\ArticleRepositoryInterface;

class IncreaseArticleNumberOfViewHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function __invoke(IncreaseArticleNumberOfViewCommand $command): void
    {
        $this->articleRepository->increaseNumberOfView($command->articleId);
    }
}