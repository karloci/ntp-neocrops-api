<?php

namespace App\Country\UseCase;

use App\Core\Service\ContextService;
use App\Country\Entity\Country;
use App\Country\Repository\CountryRepository;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FindOneCountryUseCase
{
    private ContextService $contextService;
    private CountryRepository $countryRepository;

    public function __construct(ContextService $contextService, CountryRepository $countryRepository)
    {
        $this->contextService = $contextService;
        $this->countryRepository = $countryRepository;
    }

    public function execute(string $countryId): Country
    {
        try {
            /** @var Country $country */
            $country = $this->contextService->cache->get("findOneCountry_{$countryId}", function () use ($countryId) {
                return $this->countryRepository->findOneCountry($countryId);
            });

            if (is_null($country)) {
                throw new NotFoundHttpException();
            }

            if (!$this->contextService->security->isGranted("READ", $country)) {
                throw new AccessDeniedHttpException();
            }

            return $country;
        }
        catch (InvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }
}
