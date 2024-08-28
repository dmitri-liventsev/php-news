<?php

namespace App\Tests\News\Application\Command\Handler;

use App\News\Application\Command\DeleteCommentCommand;
use App\News\Application\Command\Handler\DeleteCommentHandler;
use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Comment;
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

        $this->handler = new DeleteCommentHandler($this->commentRepository);
    }

    public function testDeleteComment(): void
    {
        $category = CategoryHelper::buildCategory();
        $this->categoryRepository->save($category);
        $article = ArticleHelper::buildArticle($category);
        $this->articleRepository->save($article);

        $comment = CommentHelper::buildComment($article);
        $this->commentRepository->save($comment);

        $this->assertNotNull($this->commentRepository->findById($comment->getId()->getValue()));

        $command = new DeleteCommentCommand($comment->getId());

        ($this->handler)($command);
        $this->entityManager->clear();

        $article = $this->articleRepository->findById($article->getId());
        $deletedComment = $this->commentRepository->find($comment->getId()->getValue());

        $this->assertNull($deletedComment, 'Comment was not deleted.');
        $this->assertNotNull($article, 'Article was deleted.');
    }
}
