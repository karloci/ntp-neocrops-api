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
    private TranslatorInterface $translator;
    private RequestStack $requestStack;

    public function __construct(EntityManagerInterface $entityManager, Security $security, CacheInterface $cache, TranslatorInterface $translator, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->cache = $cache;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
    }

    public function translate(string $key): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (is_null($request) || str_starts_with($request->getLocale(), "en")) {
            return $key;
        }

        return $this->translator->trans($key, [], "messages", $request->getLocale());
    }
}