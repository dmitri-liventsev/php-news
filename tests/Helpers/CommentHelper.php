<?php

namespace App\Tests\Helpers;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Comment;
use App\News\Domain\ValueObject\CommentAuthor;
use App\News\Domain\ValueObject\CommentContent;

class CommentHelper
{
    public static function buildComment(Article $article): Comment
    {
        return $article->addComment(
            new CommentAuthor('Test Author'),
            new CommentContent('This is a test comment.'),
        );
    }
}
