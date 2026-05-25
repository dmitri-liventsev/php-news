<?php

namespace App\News\Application\Command\Handler;

use App\News\Application\Command\UpdateArticleCommand;
use App\News\Domain\Entity\Article;
use App\News\Domain\Exception\ArticleNotFoundException;
use App\News\Domain\Exception\ImageNotFoundException;
use App\News\Domain\Repository\ArticleRepositoryInterface;
use App\News\Domain\Repository\CategoryRepositoryInterface;
use App\News\Domain\Repository\ImageRepositoryInterface;
use App\News\Domain\ValueObject\ArticleContent;
use App\News\Domain\ValueObject\ArticleTitle;
use App\News\Domain\ValueObject\ImageID;
use App\News\Domain\ValueObject\ShortDescription;

class UpdateArticleHandler
{
    public function __construct(
        private readonly ArticleRepositoryInterface  $articleRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ImageRepositoryInterface    $imageRepository,
    ) {
    }

    public function __invoke(UpdateArticleCommand $command): void
    {
        $article = $this->articleRepository->findById($command->articleID);
        if (!$article) {
            throw ArticleNotFoundException::byId($command->articleID);
        }

        $this->applyChanges($article, $command);
        $this->articleRepository->save($article);
    }

    private function applyChanges(Article $article, UpdateArticleCommand $command): void
    {
        $article->rename(new ArticleTitle($command->title));
        $article->changeShortDescription(new ShortDescription($command->shortDescription));
        $article->rewriteContent(new ArticleContent($command->content));

        if ($command->imageID !== null) {
            $imageID = new ImageID($command->imageID);
            $image = $this->imageRepository->findById($imageID);
            if (!$image) {
                throw ImageNotFoundException::byId($imageID);
            }
            $article->changeImage($image);
        } else {
            $article->changeImage(null);
        }

        $categories = $this->categoryRepository->findByIds($command->categories);
        $article->assignToCategories(...$categories);
    }
}
