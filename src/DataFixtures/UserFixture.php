<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker=Factory::create();
        for($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setFullName($faker->name);
            $user->setUserName($faker->userName);
            $user->setEmail($faker->email);
            $user->setPhone($faker->phoneNumber);
            $user->setPassword($faker->password);
            $manager->persist($user);

        }

        $manager->flush();
    }
}
