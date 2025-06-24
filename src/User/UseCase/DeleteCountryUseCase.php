<?php

namespace App\Country\UseCase;

use App\Core\Service\ContextService;
use App\Country\Entity\Country;
use App\Country\Repository\CountryRepository;
use Exception;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteCountryUseCase
{
    private ContextService $contextService;
    private CountryRepository $countryRepository;

    public function __construct(ContextService $contextService, CountryRepository $countryRepository)
    {
        $this->contextService = $contextService;
        $this->countryRepository = $countryRepository;
    }

    public function execute(string $countryId): void
    {
        try {
            /** @var Country $country */
            $country = $this->contextService->cache->get("findOneCountry_{$countryId}", function () use ($countryId) {
                return $this->countryRepository->findOneCountry($countryId);
            });
        }
        catch (InvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }

        if (is_null($country)) {
            throw new NotFoundHttpException();
        }

        if (!$this->contextService->security->isGranted("DELETE", $country)) {
            throw new AccessDeniedHttpException();
        }

        if ($country->isDeleted()) {
            return;
        }

        try {
            $country->markAsDeleted();
            $this->countryRepository->save($country, true);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        try {
            $this->contextService->cache->delete("findAllCountries");
            $this->contextService->cache->delete("findOneCountry_{$countryId}");
            $this->contextService->cache->delete("findAllCitiesByCountry_{$countryId}");
            $this->contextService->cache->delete("findAllTimezonesByCountry_{$countryId}");
        }
        catch (InvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }
}
