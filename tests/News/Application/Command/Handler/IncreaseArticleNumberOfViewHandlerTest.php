<?php

namespace App\Tests\News\Application\Command\Handler;

use App\News\Application\Command\IncreaseArticleNumberOfViewCommand;
use App\News\Application\Command\Handler\IncreaseArticleNumberOfViewHandler;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\Tests\Helpers\ArticleHelper;
use App\Tests\Helpers\CategoryHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IncreaseArticleNumberOfViewHandlerTest extends KernelTestCase
{
    private ArticleRepositoryInterface $articleRepository;
    private IncreaseArticleNumberOfViewHandler $handler;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->articleRepository = self::getContainer()->get(ArticleRepositoryInterface::class);
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();

        $this->handler = new IncreaseArticleNumberOfViewHandler($this->articleRepository);
    }

    public function testIncreaseArticleNumberOfView(): void
    {
        $category = CategoryHelper::buildCategory();
        $this->entityManager->persist($category);
        $article = ArticleHelper::buildArticle($category);
        $this->articleRepository->save($article);

        $initialViews = $article->getNumberOfViews();
        $this->assertEquals(0, $initialViews);

        $command = new IncreaseArticleNumberOfViewCommand($article->getId());

        ($this->handler)($command);
        $this->entityManager->clear();

        $updatedArticle = $this->articleRepository->findById($article->getId());
        $this->assertEquals($initialViews + 1, $updatedArticle->getNumberOfViews());
    }
}