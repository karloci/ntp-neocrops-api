<?php

namespace App\Core\UseCase;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

abstract class AbstractUseCase implements ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->container->get(EntityManagerInterface::class);
    }

    protected function getSecurity(): Security
    {
        return $this->container->get(Security::class);
    }
}

