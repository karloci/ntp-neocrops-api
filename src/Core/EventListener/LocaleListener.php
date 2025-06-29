<?php

namespace App\Core\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LocaleListener
{
    private TranslatorInterface $translator;
    private string $defaultLocale;

    public function __construct(TranslatorInterface $translator, string $defaultLocale = "en")
    {
        $this->translator = $translator;
        $this->defaultLocale = $defaultLocale;
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $locale = $request->query->get("locale");

        if (!$locale) {
            $locale = $request->getPreferredLanguage();
        }

        if (!$locale) {
            $locale = $this->defaultLocale;
        }

        $request->setLocale($locale);

        $this->translator->setLocale($locale);
    }
}
