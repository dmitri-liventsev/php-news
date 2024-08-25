<?php

namespace App\News\Infrastructure\DataFixtures;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $categories = [];
        for ($i = 0; $i < 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->word);
            $category->setCreatedAt(new \DateTime());
            $category->setUpdatedAt(new \DateTime());
            $category->setDeletedAt(null);

            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 0; $i < 5; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence);
            $article->setShortDescription($faker->text(200));
            $article->setContent($faker->text(1000));
            $article->setNumberOfViews($faker->numberBetween(0, 1000));
            $article->setCreatedAt(new \DateTime());
            $article->setUpdatedAt(new \DateTime());
            $article->setDeletedAt(null);
            $article->setIsTop($faker->boolean);

            foreach ($faker->randomElements($categories, 2) as $category) {
                $article->addCategory($category);
            }

            $manager->persist($article);
        }

        $manager->flush();
    }
}
