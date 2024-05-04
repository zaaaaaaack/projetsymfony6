<?php

namespace App\DataFixtures;

use App\Entity\Formulaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FormFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker=Factory::create();
        for($i=0; $i<20; $i++){
            $formulaire = new Formulaire();
            $formulaire->setUserName($faker->name);
            $formulaire->setEmail($faker->email);
            $formulaire->setMessage($faker->text);
            $manager->persist($formulaire);
        }
        $manager->flush();
    }
}
