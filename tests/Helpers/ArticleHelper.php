<?php

namespace App\Tests\Helpers;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Category;

class ArticleHelper
{
    public static function buildArticle(Category$category): Article
    {
        $article = new Article();
        $article->setTitle('title')
            ->setIsTop(false)
            ->addCategory($category)
            ->setShortDescription("short description")
            ->setContent("content")
            ->setNumberOfViews(0)
            ->setUpdatedAt(new \DateTime())
            ->setCreatedAt(new \DateTime('now'));

        return $article;
    }
}