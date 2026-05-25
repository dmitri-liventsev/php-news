<?php

namespace App\Tests\Helpers;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Category;
use App\News\Domain\ValueObject\ArticleContent;
use App\News\Domain\ValueObject\ArticleTitle;
use App\News\Domain\ValueObject\ShortDescription;
use DateTimeInterface;
use ReflectionProperty;

class ArticleHelper
{
    public static function buildArticle(Category $category, ?DateTimeInterface $createdAt = null): Article
    {
        $article = Article::create(
            new ArticleTitle('title'),
            new ShortDescription('short description'),
            new ArticleContent('content'),
            null,
            [$category],
        );

        if ($createdAt !== null) {
            (new ReflectionProperty(Article::class, 'createdAt'))->setValue($article, $createdAt);
        }

        return $article;
    }
}