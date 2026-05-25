<?php

namespace App\News\Application\EventListener;

use App\News\Domain\Event\ArticleCreated;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Service\TopArticlesPolicy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ArticleCreated::class)]
final class RecomputeTopArticlesOnArticleCreated
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly TopArticlesPolicy          $policy,
        private readonly EntityManagerInterface     $entityManager,
    ) {
    }

    public function __invoke(ArticleCreated $event): void
    {
        foreach ($event->categoryIDs as $categoryID) {
            $currentTop = $this->articleRepository->findCurrentTopByCategory($categoryID);
            $latest = $this->articleRepository->findLatestByCategory($categoryID, $this->policy->size());

            $this->policy->apply($currentTop, $latest);
        }

        $this->entityManager->flush();
    }
}
