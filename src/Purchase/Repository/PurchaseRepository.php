<?php

namespace App\Purchase\Repository;

use App\Core\Repository\AbstractRepository;
use App\Farm\Entity\Farm;
use App\Purchase\Entity\Purchase;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function findAllPurchasesByFarm(Farm $farm)
    {
        return $this->createQueryBuilder("purchase")
            ->addSelect("supply")
            ->leftJoin("purchase.supply", "supply")
            ->andWhere("purchase.farm = :farm")
            ->setParameter("farm", $farm)
            ->getQuery()
            ->getResult();
    }

    public function findAllPurchasesByFarmGroupBySupply(Farm $farm)
    {
        return $this->createQueryBuilder("purchase")
            ->select("supply.id AS supplyId, supply.name, supply.measureUnit, supply.manufacturer")
            ->addSelect("SUM(purchase.amount) AS totalAmount")
            ->leftJoin("purchase.supply", "supply")
            ->andWhere("purchase.farm = :farm")
            ->setParameter("farm", $farm)
            ->groupBy("supplyId, supply.name, supply.measureUnit, supply.manufacturer")
            ->getQuery()
            ->getResult();

    }
}