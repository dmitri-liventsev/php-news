<?php

namespace App\Tests\Helpers;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Comment;

class CommentHelper
{
    public static function buildComment(Article $article): Comment {
        $comment = new Comment();
        $comment->setAuthor('Test Author')
            ->setContent('This is a test comment.')
            ->setArticle($article)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        return $comment;
    }
}