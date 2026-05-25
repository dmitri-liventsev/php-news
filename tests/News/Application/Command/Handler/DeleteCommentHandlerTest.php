<?php

namespace App\Tests\News\Application\Command\Handler;

use App\News\Application\Command\DeleteCommentCommand;
use App\News\Application\Command\Handler\DeleteCommentHandler;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\Repository\CommentRepositoryInterface;
use App\Tests\Helpers\ArticleHelper;
use App\Tests\Helpers\CategoryHelper;
use App\Tests\Helpers\CommentHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteCommentHandlerTest extends KernelTestCase
{
    private ArticleRepositoryInterface $articleRepository;
    private CommentRepositoryInterface $commentRepository;
    private CategoryRepositoryInterface $categoryRepository;
    private DeleteCommentHandler $handler;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->articleRepository = self::getContainer()->get(ArticleRepositoryInterface::class);
        $this->commentRepository = self::getContainer()->get(CommentRepositoryInterface::class);
        $this->categoryRepository = self::getContainer()->get(CategoryRepositoryInterface::class);
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();

        $this->handler = new DeleteCommentHandler($this->commentRepository, $this->articleRepository);
    }

    public function testDeleteComment(): void
    {
        $category = CategoryHelper::buildCategory();
        $this->categoryRepository->save($category);
        $article = ArticleHelper::buildArticle($category);
        $this->articleRepository->save($article);

        $comment = CommentHelper::buildComment($article);
        $this->articleRepository->save($article);

        $this->assertNotNull($this->commentRepository->findById($comment->getId()));

        $command = new DeleteCommentCommand($comment->getId());

        ($this->handler)($command);
        $this->entityManager->clear();

        $article = $this->articleRepository->findById($article->getId());
        $deletedComment = $this->commentRepository->findById($comment->getId());

        $this->assertNull($deletedComment, 'Comment was not deleted.');
        $this->assertNotNull($article, 'Article was deleted.');
    }
}
