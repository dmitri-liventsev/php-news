<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\CreateArticleCommand;
use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Image;
use App\News\Domain\Exception\ImageNotFoundException;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\Repository\ImageRepositoryInterface;
use App\News\Domain\ValueObject\ArticleContent;
use App\News\Domain\ValueObject\ArticleID;
use App\News\Domain\ValueObject\ArticleTitle;
use App\News\Domain\ValueObject\ImageID;
use App\News\Domain\ValueObject\ShortDescription;

class CreateArticleHandler
{
    public function __construct(
        private readonly ArticleRepositoryInterface  $articleRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ImageRepositoryInterface    $imageRepository,
    ) {
    }

    public function __invoke(CreateArticleCommand $command): ArticleID
    {
        $image = null;
        if ($command->imageID) {
            $imageID = new ImageID($command->imageID);
            $image = $this->imageRepository->findById($imageID);
            if (!$image) {
                throw ImageNotFoundException::byId($imageID);
            }
        }

        $categories = $this->categoryRepository->findByIds($command->categories);
        $article = $this->buildArticle($command, $image, $categories);

        return $this->articleRepository->save($article);
    }

    private function buildArticle(CreateArticleCommand $command, ?Image $image, array $categories): Article
    {
        return Article::create(
            new ArticleTitle($command->title),
            new ShortDescription($command->shortDescription),
            new ArticleContent($command->content),
            $image,
            $categories,
        );
    }
}
