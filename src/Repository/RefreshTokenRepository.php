<?php

namespace App\Repository;

use App\Entity\RefreshToken;
use Doctrine\Persistence\ManagerRegistry;

class RefreshTokenRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }
}
