<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $hasher
    ){

    }
    public function load(ObjectManager $manager): void
    {
        $factory=Factory::create();
        // $product = new Product();
        // $manager->persist($product);
        $admin1 = new User();
        $admin1->setEmail("admin1@gmail.com");
        $admin1->setUserName("Nur");
        $admin1->setPhone($factory->phoneNumber);
        $admin1->setFullName($factory->name);
        $admin1->setPassword($factory->password);
        $admin1->setPassword($this->hasher->hashPassword($admin1,"admin"));
        $admin1->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin1);

        $manager->flush();
    }
    public static function getGroups():array
    {
        return ['Login'];
    }
}
