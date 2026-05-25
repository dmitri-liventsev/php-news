<?php

namespace App\Tests\Helpers;

use App\News\Domain\Entity\Category;
use App\News\Domain\ValueObject\CategoryTitle;

class CategoryHelper
{
    public static function buildCategory(): Category
    {
        return Category::create(new CategoryTitle('Test Category'));
    }
}