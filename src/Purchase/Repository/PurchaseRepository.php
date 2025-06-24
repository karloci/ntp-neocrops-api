<?php

namespace App\Purchase\Repository;

use App\Core\Repository\AbstractRepository;
use App\Purchase\Entity\Purchase;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }
}