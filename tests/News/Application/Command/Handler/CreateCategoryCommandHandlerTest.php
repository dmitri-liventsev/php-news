<?php

namespace App\Tests\News\Application\Command\Handler;

use App\News\Application\Command\CreateCategoryCommand;
use App\News\Application\Command\Handler\CreateCategoryHandler;

use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;

class CreateCategoryCommandHandlerTest extends TestCase
{
    private CreateCategoryHandler $handler;
    private CategoryRepositoryInterface $categoryRepository;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = $this->get(CategoryRepositoryInterface::class);
        $this->handler = new CreateCategoryHandler($this->categoryRepository);
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    public function testCreateCategory(): void
    {
        $initialCount = count($this->categoryRepository->findAll());

        $command = new CreateCategoryCommand('New Category');
        ($this->handler)($command);
        $this->entityManager->clear();

        $categories = $this->categoryRepository->findAll();
        $this->assertCount($initialCount + 1, $categories);
    }
}
