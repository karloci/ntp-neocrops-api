<?php

namespace App\Core\DataFixtures;

use App\Entity\Farm;
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
        $testFarm = new Farm();
        $testFarm->setName('Test Farm');
        $testFarm->setOib("66321597535");
        $testFarm->setCountryIsoCode("HR");
        $testFarm->setPostalCode("10360");

        $manager->persist($testFarm);

        $testUser = new User();
        $testUser->setFullName("Test Test");
        $testUser->setEmail("test@neocrops.com");
        $testUser->setPassword($this->passwordHasher->hashPassword($testUser, "test"));
        $testUser->setUserFarm($testFarm);

        $manager->persist($testUser);
        $manager->flush();
    }
}
