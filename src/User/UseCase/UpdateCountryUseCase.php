<?php

namespace App\Country\UseCase;

use App\Core\Service\ContextService;
use App\Country\Dto\UserDto;
use App\Country\Entity\Country;
use App\Country\Repository\CountryRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateCountryUseCase
{
    private ContextService $contextService;
    private CountryRepository $countryRepository;

    public function __construct(ContextService $contextService, CountryRepository $countryRepository)
    {
        $this->contextService = $contextService;
        $this->countryRepository = $countryRepository;
    }

    public function execute(string $countryId, UserDto $countryDto): Country
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

        if (!$this->contextService->security->isGranted("UPDATE", $country)) {
            throw new AccessDeniedHttpException();
        }

        try {
            $country->setName($countryDto->getName());
            $country->setCode(mb_strtoupper($countryDto->getCode()));

            $this->countryRepository->save($country, true);
        }
        catch (UniqueConstraintViolationException) {
            throw new ConflictHttpException();
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        try {
            $this->contextService->cache->delete("findAllCountries");
            $this->contextService->cache->delete("findOneCountry_{$countryId}");
        }
        catch (InvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }

        return $country;
    }
}
