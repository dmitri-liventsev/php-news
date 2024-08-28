<?php

namespace App\Tests\News\Application\Command\Handler;

use App\News\Application\Command\Handler\UpdateArticleHandler;
use App\News\Application\Command\UpdateArticleCommand;
use App\News\Application\Query\Handler\DTO\ArticleDTO;
use App\News\Domain\Entity\Article;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\Repository\ImageRepositoryInterface;
use App\Tests\Helpers\ArticleHelper;
use App\Tests\Helpers\CategoryHelper;
use App\Tests\Helpers\ImageHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateArticleHandlerTest extends KernelTestCase
{
    private ArticleRepositoryInterface $articleRepository;
    private CategoryRepositoryInterface $categoryRepository;
    private ImageRepositoryInterface $imageRepository;
    private UpdateArticleHandler $handler;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->articleRepository = self::getContainer()->get(ArticleRepositoryInterface::class);
        $this->categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);
        $this->imageRepository = self::getContainer()->get(ImageRepositoryInterface::class);
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();

        $this->handler = new UpdateArticleHandler(
            $this->articleRepository,
            $this->categoryRepository,
            $this->imageRepository,
            $this->entityManager
        );
    }

    public function testUpdateArticleRemovesImage(): void
    {
        $category = CategoryHelper::buildCategory();
        $this->categoryRepository->save($category);

        $image = ImageHelper::buildImage();
        $this->imageRepository->save($image);

        $newCategory = CategoryHelper::buildCategory();
        $this->categoryRepository->save($newCategory);

        $article = ArticleHelper::buildArticle($category);
        $article->setImage($image);
        $this->articleRepository->save($article);

        $this->assertNotNull($article->getImage());

        $command = new UpdateArticleCommand(
            $article->getId(),
            'Updated Title',
            'Updated Short Description',
            'Updated Content',
            null,
            [$newCategory->getId()]
        );

        ($this->handler)($command);
        $this->entityManager->clear();


        /** @var Article $updatedArticle */
        $updatedArticle = $this->articleRepository->findById($article->getId());

        $this->assertNull($updatedArticle->getImage(), 'Image was not removed from the article.');
        $this->assertEquals('Updated Title', $updatedArticle->getTitle(), 'Title was not updated.');
        $this->assertEquals('Updated Short Description', $updatedArticle->getShortDescription(), 'Short description was not updated.');
        $this->assertEquals('Updated Content', $updatedArticle->getContent(), 'Content was not updated.');
        $this->assertEquals(1, $updatedArticle->getCategories()->count(), 'Category not replaced');
        $this->assertEquals($newCategory->getId()->getValue(), $updatedArticle->getCategories()->get(0)->getId()->getValue(), 'Category not replaced');
    }
}
