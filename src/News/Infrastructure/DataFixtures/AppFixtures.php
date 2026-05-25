<?php

namespace App\News\Infrastructure\DataFixtures;

use App\News\Domain\Entity\Article;
use App\News\Domain\Entity\Category;
use App\News\Domain\Entity\Image;
use App\News\Domain\ValueObject\ArticleContent;
use App\News\Domain\ValueObject\ArticleTitle;
use App\News\Domain\ValueObject\CategoryTitle;
use App\News\Domain\ValueObject\ImageFileName;
use App\News\Domain\ValueObject\ShortDescription;
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
            $category = Category::create(new CategoryTitle($faker->word));
            $manager->persist($category);
            $categories[] = $category;
        }

        $images = [];
        foreach ($this->PhotoOfHumanitysOwners as $fileName) {
            $image = Image::create(new ImageFileName($fileName));
            $manager->persist($image);
            $images[] = $image;
        }

        for ($i = 0; $i < 20; $i++) {
            $article = Article::create(
                new ArticleTitle($faker->sentence),
                new ShortDescription($faker->text()),
                new ArticleContent($faker->text(1000)),
                $images[array_rand($images)],
                $faker->randomElements($categories, 2),
            );

            $views = $faker->numberBetween(0, 1000);
            for ($v = 0; $v < $views; $v++) {
                $article->incrementViews();
            }

            if ($faker->boolean) {
                $article->markAsTop();
            }

            $manager->persist($article);
        }

        $manager->flush();
    }
}