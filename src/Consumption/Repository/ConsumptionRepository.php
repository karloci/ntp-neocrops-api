<?php

namespace App\Consumption\Repository;

use App\Core\Repository\AbstractRepository;
use App\Consumption\Entity\Consumption;
use App\Farm\Entity\Farm;
use Doctrine\Persistence\ManagerRegistry;

class ConsumptionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consumption::class);
    }

    public function findAllConsumptionsByFarm(Farm $farm)
    {
        return $this->createQueryBuilder("consumption")
            ->addSelect("supply")
            ->leftJoin("consumption.supply", "supply")
            ->andWhere("consumption.farm = :farm")
            ->setParameter("farm", $farm)
            ->getQuery()
            ->getResult();
    }

    public function findAllConsumptionsByFarmGroupBySupply(Farm $farm)
    {
        return $this->createQueryBuilder("consumption")
            ->select("supply.id AS supplyId, supply.name, supply.measureUnit, supply.manufacturer")
            ->addSelect("SUM(consumption.amount) AS totalAmount")
            ->leftJoin("consumption.supply", "supply")
            ->andWhere("consumption.farm = :farm")
            ->setParameter("farm", $farm)
            ->groupBy("supplyId, supply.name, supply.measureUnit, supply.manufacturer")
            ->getQuery()
            ->getResult();

    }
}