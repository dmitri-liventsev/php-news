<?php

namespace App\Tests\News\Application\Command\Handler;

use App\News\Application\Command\CreateCommentCommand;
use App\News\Application\Command\Handler\CreateCommentHandler;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\Repository\CommentRepositoryInterface;
use App\Tests\Helpers\ArticleHelper;
use App\Tests\Helpers\CategoryHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCommentHandlerTest extends KernelTestCase
{
    private ArticleRepositoryInterface $articleRepository;

    private CommentRepositoryInterface $commentRepository;

    private CategoryRepositoryInterface $categoryRepository;

    private CreateCommentHandler $handler;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->articleRepository = self::getContainer()->get(ArticleRepositoryInterface::class);
        $this->commentRepository = self::getContainer()->get(CommentRepositoryInterface::class);
        $this->categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
        $this->handler = new CreateCommentHandler($this->commentRepository, $this->articleRepository);
    }

    public function testCreateComment(): void
    {
        $category = CategoryHelper::buildCategory();
        $this->categoryRepository->save($category);

        $article = ArticleHelper::buildArticle($category);
        $this->articleRepository->save($article);

        $command = new CreateCommentCommand(
            $article->getId(),
            'This is a test comment.',
            'Test Author',
        );

        $commentID = ($this->handler)($command);
        $this->entityManager->clear();

        $savedComment = $this->commentRepository->find($commentID->getValue());

        $this->assertNotNull($savedComment, 'Comment was not saved.');
        $this->assertSame('Test Author', $savedComment->getAuthor());
        $this->assertSame('This is a test comment.', $savedComment->getContent());
        $this->assertSame($article->getId()->getValue(), $savedComment->getArticle()->getId()->getValue());
    }
}
