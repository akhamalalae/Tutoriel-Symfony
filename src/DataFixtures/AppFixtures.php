<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\UserFactory;
use App\Factory\MessageFactory;
use App\Factory\DiscussionFactory;
use App\Factory\DiscussionMessageUserFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ini_set('memory_limit', '1G'); // Définit la limite de mémoire à 1 Go
 
        // Création de 10 utilisateurs avec la factory
        UserFactory::new()->createMany(2);

        DiscussionFactory::new()->createMany(1);

        MessageFactory::new()->createMany(100);

        DiscussionMessageUserFactory::new()->createMany(1000);

        $manager->flush();
    }
}