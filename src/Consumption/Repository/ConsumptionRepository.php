<?php

namespace App\Consumption\Repository;

use App\Core\Repository\AbstractRepository;
use App\Consumption\Entity\Consumption;
use Doctrine\Persistence\ManagerRegistry;

class ConsumptionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consumption::class);
    }
}