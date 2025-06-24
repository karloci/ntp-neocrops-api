<?php

namespace App\Core\DataFixtures;

use App\Farm\Entity\Farm;
use App\Supply\Entity\Supply;
use App\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    private array $chemicals = [
        ["Herbicide Alpha", "l", "Bayer"],
        ["Fungicide Beta", "l", "Syngenta"],
        ["Insecticide Gamma", "l", "BASF"],
        ["Growth Regulator Delta", "l", "Corteva"],
        ["Adjuvant Epsilon", "l", "UPL"],
        ["Pesticide Zeta", "l", "Nufarm"],
        ["Miticide Eta", "l", "FMC"],
        ["Bactericide Theta", "l", "Adama"],
        ["Soil Conditioner Iota", "l", "Valent"],
        ["Desiccant Kappa", "l", "Arysta"],
    ];

    private array $fertilizers = [
        ["Urea 46%", "kg", "Yara"],
        ["NPK 15-15-15", "kg", "Petrokemija"],
        ["Ammonium Nitrate", "kg", "EuroChem"],
        ["DAP 18-46-0", "kg", "ICL"],
        ["MAP 11-52-0", "kg", "Haifa"],
        ["Potassium Sulfate", "kg", "K+S"],
        ["Calcium Nitrate", "kg", "Yara"],
        ["Liquid NPK", "l", "Timac Agro"],
        ["Micronutrient Mix", "kg", "Tradecorp"],
        ["Humic Acid", "l", "BioAg"],
    ];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $testFarm = new Farm();
        $testFarm->setName("Test Farm");
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

        $adminUser = new User();
        $adminUser->setFullName("Admin Admin");
        $adminUser->setEmail("admin@neocrops.com");
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, "admin"));
        $adminUser->setRoles(["ROLE_ADMIN"]);
        $adminUser->setUserFarm($testFarm);

        $manager->persist($adminUser);

        foreach (array_merge($this->chemicals, $this->fertilizers) as [$name, $unit, $manufacturer]) {
            $supply = new Supply();
            $supply->setName($name);
            $supply->setMeasureUnit($unit);
            $supply->setManufacturer($manufacturer);

            $manager->persist($supply);
        }

        $manager->flush();
    }
}
