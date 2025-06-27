<?php

namespace App\Consumption\UseCase;

use App\Consumption\Exception\InvalidConsumptionException;
use App\Core\Service\ContextService;
use App\Consumption\Dto\ConsumptionDto;
use App\Consumption\Entity\Consumption;
use App\Consumption\Repository\ConsumptionRepository;
use App\Inventory\Repository\InventoryRepository;
use App\Supply\Entity\Supply;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateConsumptionUseCase
{
    private ContextService $contextService;
    private ConsumptionRepository $consumptionRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(ContextService $contextService, ConsumptionRepository $consumptionRepository, InventoryRepository $inventoryRepository)
    {
        $this->contextService = $contextService;
        $this->consumptionRepository = $consumptionRepository;
        $this->inventoryRepository = $inventoryRepository;
    }

    public function execute(string $consumptionId, ConsumptionDto $consumptionDto): Consumption
    {
        /** @var Consumption $consumption */
        $consumption = $this->consumptionRepository->findOneBy(["id" => $consumptionId]);

        if (is_null($consumption)) {
            throw new NotFoundHttpException();
        }

        if (!$this->contextService->security->isGranted("UPDATE", $consumption)) {
            throw new AccessDeniedHttpException();
        }

        $currentStock = $this->inventoryRepository->findStockForSupply($consumption->getFarm(), $consumptionDto->getSupply());
        if (($consumptionDto->getAmount() - $consumption->getAmount()) > $currentStock) {
            throw new InvalidConsumptionException("Nema dovoljno na lageru!");
        }

        try {
            $consumption->setSupply($this->contextService->entityManager->getReference(Supply::class, $consumptionDto->getSupply()));
            $consumption->setAmount($consumptionDto->getAmount());
            $consumption->setDate($consumptionDto->getDate());
            $consumption->setComment($consumptionDto->getComment());

            $this->consumptionRepository->save($consumption, true);
        }
        catch (ORMException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $consumption;
    }
}
