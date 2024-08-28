<?php

namespace App\News\Application\Command\Handler;
use App\News\Application\Command\DeleteArticleCommand;
use App\News\Domain\Repository\ArticleRepositoryInterface;

class DeleteArticleHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }
    public function __invoke(DeleteArticleCommand $command): void
    {
        $this->articleRepository->deleteById($command->articleId);
    }
}