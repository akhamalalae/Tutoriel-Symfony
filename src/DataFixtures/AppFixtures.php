<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\CourseFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ini_set("memory_limit", "-1");
        for ($i = 0; $i < 1000; $i++) {
            CategoryFactory::createOne();
        }

        $manager->flush();
        $manager->clear();

        for ($i = 0; $i < 10000; $i++) {
            CourseFactory::createOne();
        }

        $manager->flush();
    }
}
