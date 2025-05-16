<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @throws UniqueConstraintViolationException
     */
    public function save(object $entity, $withFlush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($withFlush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(object $entity, $withFlush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($withFlush) {
            $this->getEntityManager()->flush();
        }
    }
}
