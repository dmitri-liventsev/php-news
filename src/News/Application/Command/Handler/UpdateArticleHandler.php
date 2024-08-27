<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\UpdateArticleCommand;
use App\News\Domain\Entity\Article;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\Repository\ImageRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PharIo\Version\Exception;

class UpdateArticleHandler
{
    private ArticleRepositoryInterface $articleRepository;

    private CategoryRepositoryInterface $categoryRepository;

    private ImageRepositoryInterface $imageRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        CategoryRepositoryInterface $categoryRepository,
        ImageRepositoryInterface $imageRepository,
        EntityManagerInterface  $entityManager
    ) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(UpdateArticleCommand $command): void
    {
        /** @var Article $article */
        $article = $this->articleRepository->find($command->articleID->getValue());
        if (!$article) {
            throw new \Exception('Article not found');
        }
        $article = $this->updateArticle($article, $command);

        $this->entityManager->beginTransaction();
        try {
            $this->articleRepository->save($article);
            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    private function updateArticle(Article $article, UpdateArticleCommand $command): Article
    {
        $article->setTitle($command->title);
        $article->setShortDescription($command->shortDescription);
        $article->setContent($command->content);
        if ($command->imageID !== null) {
            $image = $this->imageRepository->find($command->imageID);
            if (!$image) {
                throw new \Exception('Image not found');
            }
            $article->setImage($image);
        } else {
            $article->setImage(null);
        }
        $categories = $this->categoryRepository->findByIds($command->categories);
        $article->setCategories($categories);
        $article->setUpdatedAt(new \DateTime());

        return $article;
    }
}