<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\CreateArticleCommand;
use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Image;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\Repository\ImageRepositoryInterface;
use App\News\Domain\ValueObject\ArticleID;
use Doctrine\ORM\EntityManagerInterface;
use PharIo\Version\Exception;

class CreateArticleHandler
{
    const NUM_OF_TOP_ARTICLES = 3;

    private ArticleRepositoryInterface $articleRepository;

    private CategoryRepositoryInterface $categoryRepository;

    private ImageRepositoryInterface $imageRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        CategoryRepositoryInterface $categoryRepository,
        ImageRepositoryInterface $imageRepository,
        EntityManagerInterface  $entityManager,
    ) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @throws \Exception
     */
    public function __invoke(CreateArticleCommand $command): ArticleID
    {
        $image = $this->imageRepository->find($command->imageID);
        if (!$image) {
            throw new \Exception('Image not found');
        }
        $categories = $this->categoryRepository->findByIds($command->categories);
        $article = $this->buildArticle($command, $image, $categories);

        $this->entityManager->beginTransaction();
        try {
            $articleID = $this->articleRepository->save($article);
            $this->updateTopArticles($categories);

            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        return $articleID;
    }

    private function buildArticle(CreateArticleCommand $command, Image $image, array $categories): Article{
        $article = new Article();
        $article->setTitle($command->title)
            ->setShortDescription($command->shortDescription)
            ->setContent($command->content)
            ->setImage($image)
            ->setNumberOfViews(0)
            ->setCategories($categories)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $article;
    }

    private function updateTopArticles(array $categories): void
    {
        foreach ($categories as $category) {
            $this->articleRepository->resetTopArticlesByCategory($category->getId());
            $topArticles = $this->articleRepository->findTopArticlesByCategory($category->getId(), self::NUM_OF_TOP_ARTICLES);

            foreach ($topArticles as $article) {
                $article->setIsTop(true);
                $this->articleRepository->save($article);
            }
        }
    }
}