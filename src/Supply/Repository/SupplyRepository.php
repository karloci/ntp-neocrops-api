<?php

namespace App\Supply\Repository;

use App\Core\Repository\AbstractRepository;
use App\Supply\Entity\Supply;
use Doctrine\Persistence\ManagerRegistry;

class SupplyRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Supply::class);
    }

    /**
     * @return Supply[]
     */
    public function findAllSupplies(): array
    {
        return $this->createQueryBuilder("supply")
            ->orderBy("supply.name", "ASC")
            ->getQuery()
            ->getResult();
    }

    public function findOneSupply(string $supplyId): ?Supply
    {
        return $this->createQueryBuilder("supply")
            ->andWhere("supply.id = :supplyId")
            ->setParameter("supplyId", $supplyId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
