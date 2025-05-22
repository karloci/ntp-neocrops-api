<?php

namespace App\Core\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $testUser = new User();
        $testUser->setFullName("Test Test");
        $testUser->setEmail("test@neocrops.com");
        $testUser->setPassword($this->passwordHasher->hashPassword($testUser, "test"));

        $manager->persist($testUser);
        $manager->flush();
    }
}
