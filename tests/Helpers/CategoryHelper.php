<?php

namespace App\Tests\Helpers;

use App\News\Domain\Entity\Category;

class CategoryHelper
{
    public static function buildCategory(): Category {
        $category = new Category();
        $category->setTitle('Test Category')
            ->setUpdatedAt(new \DateTime())
            ->setCreatedAt(new \DateTime('now'));


        return $category;
    }
}