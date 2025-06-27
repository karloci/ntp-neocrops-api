<?php

namespace App\Inventory\Repository;

use App\Consumption\Entity\Consumption;
use App\Core\Repository\AbstractRepository;
use App\Farm\Entity\Farm;
use App\Purchase\Entity\Purchase;
use App\Supply\Entity\Supply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class InventoryRepository
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findStockForSupply(Farm|int $farm, Supply|int $supply): float
    {
        $purchaseResult = $this->em->createQueryBuilder()
            ->select("SUM(p.amount) AS totalAmount")
            ->from(Purchase::class, 'p')
            ->where("p.farm = :farm")
            ->andWhere("p.supply = :supply")
            ->setParameter("farm", $farm)
            ->setParameter("supply", $supply)
            ->getQuery()
            ->getOneOrNullResult();

        $consumptionResult = $this->em->createQueryBuilder()
            ->select("SUM(c.amount) AS totalAmount")
            ->from(Consumption::class, 'c')
            ->where("c.farm = :farm")
            ->andWhere("c.supply = :supply")
            ->setParameter("farm", $farm)
            ->setParameter("supply", $supply)
            ->getQuery()
            ->getOneOrNullResult();

        $purchaseAmount = $purchaseResult['totalAmount'] ?? 0;
        $consumptionAmount = $consumptionResult['totalAmount'] ?? 0;

        return $purchaseAmount - $consumptionAmount;
    }
}