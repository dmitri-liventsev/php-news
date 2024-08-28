<?php

namespace App\Tests\News\Application\Command\Handler;

use App\News\Application\Command\DeleteCategoryCommand;
use App\News\Application\Command\Handler\DeleteCategoryHandler;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\Tests\Helpers\ArticleHelper;
use App\Tests\Helpers\CategoryHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteCategoryHandlerTest extends KernelTestCase
{
    private ArticleRepositoryInterface $articleRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = $this->getContainer()->get(ArticleRepositoryInterface::class);
        $this->categoryRepository = $this->getContainer()->get(CategoryRepositoryInterface::class);
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    public function testDeleteCategoryWithArticle(): void
    {
        $initialCategoryCount = $this->getCategoryCount();
        $initialArticleCount = $this->getArticleCount();

        $category = CategoryHelper::buildCategory();
        $article = ArticleHelper::buildArticle($category);

        $this->categoryRepository->save($category);
        $this->articleRepository->save($article);
        $this->entityManager->clear();

        $command = new DeleteCategoryCommand($category->getId());
        $handler = new DeleteCategoryHandler($this->categoryRepository);
        $handler($command);

        $this->entityManager->clear();

        $finalCategoryCount = $this->getCategoryCount();
        $finalArticleCount = $this->getArticleCount();

        $article = $this->articleRepository->find($article->getId()->getValue());

        $this->assertNotNull($article, 'Article should not be deleted.');
        $this->assertSame($initialCategoryCount, $finalCategoryCount, 'Category was not deleted correctly.');
        $this->assertSame($initialArticleCount+1, $finalArticleCount, 'Article should not be deleted.');
    }

    private function getCategoryCount(): int
    {
        return (int) $this->entityManager->createQuery('SELECT COUNT(c.id) FROM App\News\Domain\Entity\Category c')
            ->getSingleScalarResult();
    }

    private function getArticleCount(): int
    {
        return (int) $this->entityManager->createQuery('SELECT COUNT(a.id) FROM App\News\Domain\Entity\Article a')
            ->getSingleScalarResult();
    }
}
