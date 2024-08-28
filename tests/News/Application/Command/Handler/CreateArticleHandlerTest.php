<?php

namespace App\Tests\News\Application\Command\Handler;


use App\News\Application\Command\CreateArticleCommand;
use App\News\Application\Command\Handler\CreateArticleHandler;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\Repository\ImageRepositoryInterface;
use App\Tests\Helpers\ArticleHelper;
use App\Tests\Helpers\CategoryHelper;
use App\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;

class CreateArticleHandlerTest extends TestCase
{
    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ImageRepositoryInterface
     */
    private ImageRepositoryInterface $imageRepository;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = $this->getContainer()->get(ArticleRepositoryInterface::class);
        $this->categoryRepository = $this->getContainer()->get(CategoryRepositoryInterface::class);
        $this->imageRepository = $this->getContainer()->get(ImageRepositoryInterface::class);
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    public function testHandleUpdatesTopArticlesCorrectly()
    {
        $handler = new CreateArticleHandler(
            $this->articleRepository,
            $this->categoryRepository,
            $this->imageRepository,
            $this->entityManager
        );

        $this->entityManager->createQuery('DELETE FROM App\News\Domain\Entity\Article')->execute();

        $category = CategoryHelper::buildCategory();
        $this->categoryRepository->save($category);

        $oldArticle = ArticleHelper::buildArticle($category)->setCreatedAt(new \DateTime('-3 days'));

        $oldArticleID = $this->articleRepository->save($oldArticle);

        $newArticle1 = ArticleHelper::buildArticle($category)->setCreatedAt(new \DateTime('-2 days'));
        $newArticle1ID = $this->articleRepository->save($newArticle1);

        $newArticle2 = ArticleHelper::buildArticle($category)->setCreatedAt(new \DateTime('-1 day'));
        $newArticle2ID = $this->articleRepository->save($newArticle2);

        $newArticle3 = ArticleHelper::buildArticle($category)->setCreatedAt(new \DateTime('now'));

        $command = new CreateArticleCommand(
            $newArticle3->getTitle(),
            $newArticle3->getShortDescription(),
            $newArticle3->getContent(),
            null,
            [$category->getId()],
        );

        $newArticleID = ($handler)($command);
        $this->entityManager->clear();

        $oldArticle = $this->articleRepository->findById($oldArticleID);
        $newArticle1 = $this->articleRepository->findById($newArticle1ID);
        $newArticle2 = $this->articleRepository->findById($newArticle2ID);
        $newArticle3 = $this->articleRepository->findById($newArticleID);

        $this->assertFalse($oldArticle->getIsTop(), 'The oldest article should no longer be top.');
        $this->assertTrue($newArticle1->getIsTop(), 'The second article should remain top.');
        $this->assertTrue($newArticle2->getIsTop(), 'The third article should remain top.');
        $this->assertTrue($newArticle3->getIsTop(), 'The new article should be marked as top.');
    }
}
