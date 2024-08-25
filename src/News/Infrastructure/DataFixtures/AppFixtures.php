<?php

namespace App\News\Infrastructure\DataFixtures;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Category;
use App\News\Domain\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private array $PhotoOfHumanitysOwners = [
        "cat_1.jpeg",
        "cat_2.jpeg",
        "cat_3.jpeg",
        "cat_4.jpeg",
        "cat_5.jpeg",
        "cat_6.jpeg",
        "cat_7.jpeg",
        "cat_8.jpeg",
        "cat_9.jpeg",
        "cat_10.jpeg",
        "cat_11.jpeg",
        "cat_12.jpeg",
        "cat_13.jpeg",
        "cat_14.jpeg",
        "cat_15.jpeg",
        "cat_16.jpeg",
        "cat_17.jpeg",
        "cat_18.jpeg",
        "cat_19.jpeg",
        "cat_20.jpeg",
    ];

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

        $images = [];
        foreach($this->PhotoOfHumanitysOwners as $fileName) {
            $image = new Image();
            $image->setFileName($fileName);
            $image->setCreatedAt(new \DateTime());
            $image->setUpdatedAt(new \DateTime());
            $image->setDeletedAt(null);

            $manager->persist($image);
            $images[] = $image;
        }

        for ($i = 0; $i < 10; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence);
            $article->setShortDescription($faker->text(200));
            $article->setContent($faker->text(1000));
            $article->setNumberOfViews($faker->numberBetween(0, 1000));
            $article->setCreatedAt(new \DateTime());
            $article->setUpdatedAt(new \DateTime());
            $article->setDeletedAt(null);
            $article->setIsTop($faker->boolean);

            $randomKey = array_rand($images);
            $article->setImage($images[$randomKey]);

            foreach ($faker->randomElements($categories, 2) as $category) {
                $article->addCategory($category);
            }

            $manager->persist($article);
        }

        $manager->flush();
    }
}
