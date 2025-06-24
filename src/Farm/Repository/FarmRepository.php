<?php

namespace App\Farm\Repository;

use App\Core\Repository\AbstractRepository;
use App\Farm\Entity\Farm;
use Doctrine\Persistence\ManagerRegistry;

class FarmRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Farm::class);
    }
}
