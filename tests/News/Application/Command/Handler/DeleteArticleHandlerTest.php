<?php

namespace App\Tests\News\Application\Command\Handler;


use App\News\Application\Command\DeleteArticleCommand;
use App\News\Application\Command\Handler\DeleteArticleHandler;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\Tests\Helpers\ArticleHelper;
use App\Tests\Helpers\CategoryHelper;
use App\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;

class DeleteArticleHandlerTest extends TestCase
{
    private ArticleRepositoryInterface $articleRepository;

    private CategoryRepositoryInterface $categoryRepository;
    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        parent::setUp();

        $this->articleRepository = $this->get(ArticleRepositoryInterface::class);
        $this->categoryRepository = $this->getContainer()->get(CategoryRepositoryInterface::class);
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    public function testDeleteArticle(): void
    {
        $category = CategoryHelper::buildCategory();
        $this->categoryRepository->save($category);
        $initialCount = $this->getArticleCount();

        $article = ArticleHelper::buildArticle($category);
        $this->articleRepository->save($article);

        $addedCount = $this->getArticleCount();
        $this->assertSame($initialCount + 1, $addedCount, 'Article was not added correctly.');

        $handler = new DeleteArticleHandler($this->articleRepository);
        $command = new DeleteArticleCommand($article->getId());
        $handler($command);

        $this->entityManager->clear();

        $finalCount = $this->getArticleCount();
        $this->assertSame($initialCount, $finalCount, 'Article was not deleted correctly.');
    }

    private function getArticleCount(): int
    {
        return (int) $this->entityManager->createQuery('SELECT COUNT(a.id) FROM App\News\Domain\Entity\Article a')
            ->getSingleScalarResult();
    }
}
