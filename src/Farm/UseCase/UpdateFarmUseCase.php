<?php

namespace App\Farm\UseCase;

use App\Core\Service\ContextService;
use App\Farm\Dto\FarmDto;
use App\Farm\Exception\InvalidConsumptionException;
use App\Profile\Dto\UpdateProfileDto;
use App\Farm\Entity\Farm;
use App\Farm\Repository\FarmRepository;
use App\User\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateFarmUseCase
{
    private ContextService $contextService;
    private FarmRepository $farmRepository;

    public function __construct(ContextService $contextService, FarmRepository $farmRepository)
    {
        $this->contextService = $contextService;
        $this->farmRepository = $farmRepository;
    }

    public function execute(FarmDto $farmDto): Farm
    {
        /** @var User $user */
        $user = $this->contextService->security->getUser();

        if (!$this->contextService->security->isGranted("ROLE_ADMIN")) {
            throw new AccessDeniedHttpException();
        }

        try {
            $farm = $user->getUserFarm();
            $farm->setName($farmDto->getName());
            $farm->setOib($farmDto->getOib());
            $farm->setCountryIsoCode($farmDto->getCountryIsoCode());
            $farm->setPostalCode($farmDto->getPostalCode());

            $this->farmRepository->save($farm, true);

            return $farm;
        }
        catch (UniqueConstraintViolationException) {
            throw new InvalidConsumptionException($this->contextService->translate("Farm with this ID already exists"));
        }
        catch (ORMException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}