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
        ["Laudis OD", "L", "Bayer"],
        ["Revus Top", "L", "Syngenta"],
        ["Fastac SC", "L", "BASF"],
        ["Moddus", "L", "Corteva"],
        ["Break-Thru", "L", "UPL"],
        ["Tazer 250 SC", "L", "Nufarm"],
        ["Omite", "L", "FMC"],
        ["Custodia", "L", "Adama"],
        ["Preside SC", "L", "Valent"],
        ["Desica", "L", "Arysta"],
    ];

    private array $fertilizers = [
        ["YaraVera Urea 46%", "kg", "Yara"],
        ["KAN 27% (NPK 15-15-15)", "kg", "Petrokemija"],
        ["Nitrophoska Special", "kg", "EuroChem"],
        ["Agrocote DAP 18-46-0", "kg", "ICL"],
        ["Poly-Feed MAP 11-52-0", "kg", "Haifa"],
        ["Kali SOP (Potassium Sulfate)", "kg", "K+S"],
        ["YaraLiva Tropicote (Calcium Nitrate)", "kg", "Yara"],
        ["Fertileader Elite (Liquid NPK)", "L", "Timac Agro"],
        ["Nutricomplex Mix TE", "kg", "Tradecorp"],
        ["HumaGro Humic Acid", "L", "BioAg"],
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
