<?php

namespace App\Authentication\Repository;

use App\Authentication\Entity\RefreshToken;
use App\Core\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class RefreshTokenRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }
}
