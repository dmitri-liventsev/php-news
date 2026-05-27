<?php

namespace App\Mudimind\Infrastructure\DataFixtures;

use App\Mudimind\Domain\Entity\Masseur;
use App\Mudimind\Domain\ValueObject\MasseurName;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MasseurFixtures extends Fixture
{
    private const NAMES = ['Anna', 'Bjorn', 'Clara', 'Dmitri'];

    public function load(ObjectManager $manager): void
    {
        foreach (self::NAMES as $name) {
            $manager->persist(Masseur::create(new MasseurName($name)));
        }

        $manager->flush();
    }
}