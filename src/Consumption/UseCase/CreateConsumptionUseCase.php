<?php

namespace App\Consumption\UseCase;

use App\Core\Service\ContextService;
use App\Farm\Entity\Farm;
use App\Consumption\Dto\ConsumptionDto;
use App\Consumption\Entity\Consumption;
use App\Consumption\Repository\ConsumptionRepository;
use App\Supply\Entity\Supply;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CreateConsumptionUseCase
{
    private ContextService $contextService;
    private ConsumptionRepository $consumptionRepository;

    public function __construct(ContextService $contextService, ConsumptionRepository $consumptionRepository)
    {
        $this->contextService = $contextService;
        $this->consumptionRepository = $consumptionRepository;
    }

    public function execute(ConsumptionDto $consumptionDto, Farm $farm): Consumption
    {
        if (!$this->contextService->security->isGranted("READ", $farm)) {
            throw new AccessDeniedHttpException();
        }

        // TODO: provjera je li ima dovoljno na lageru

        try {
            $consumption = new Consumption();
            $consumption->setSupply($this->contextService->entityManager->getReference(Supply::class, $consumptionDto->getSupply()));
            $consumption->setAmount($consumptionDto->getAmount());
            $consumption->setDate($consumptionDto->getDate());
            $consumption->setComment($consumptionDto->getComment());
            $consumption->setFarm($farm);

            $this->consumptionRepository->save($consumption, true);

            return $consumption;
        }
        catch (ORMException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}
