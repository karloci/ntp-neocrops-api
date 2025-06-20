<?php

namespace App\Core\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContextService
{
    public EntityManagerInterface $entityManager;
    public Security $security;
    public CacheInterface $cache;
    private RequestStack $requestStack;

    public function __construct(EntityManagerInterface $entityManager, Security $security, CacheInterface $cache, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->cache = $cache;
        $this->requestStack = $requestStack;
    }

    public function translate(string $key): string
    {
        return $key;
    }
}