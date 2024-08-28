<?php

namespace App\Tests\News\Application\Command\Handler;

use App\News\Application\Command\Handler\UpdateCategoryHandler;
use App\News\Application\Command\UpdateCategoryCommand;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\Tests\Helpers\CategoryHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateCategoryHandlerTest extends KernelTestCase
{
    private CategoryRepositoryInterface $categoryRepository;
    private UpdateCategoryHandler $handler;
    private EntityManagerInterface$entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->handler = new UpdateCategoryHandler($this->categoryRepository);
    }

    public function testUpdateCategory(): void
    {
        $category = CategoryHelper::buildCategory('Original Title');
        $this->categoryRepository->save($category);

        $originalUpdatedAt = $category->getUpdatedAt();
        $newTitle = 'Updated Title';

        $command = new UpdateCategoryCommand($category->getId(), $newTitle);

        ($this->handler)($command);
        $this->entityManager->clear();

        $updatedCategory = $this->categoryRepository->find($category->getId()->getValue());
        $this->assertEquals($newTitle, $updatedCategory->getTitle(), 'Category title was not updated.');
        $this->assertNotEquals($originalUpdatedAt, $updatedCategory->getUpdatedAt(), 'Category updatedAt was not updated.');
    }
}
