<?php

namespace App\Consumption\UseCase;

use App\Consumption\Exception\InvalidConsumptionException;
use App\Core\Service\ContextService;
use App\Consumption\Entity\Consumption;
use App\Consumption\Repository\ConsumptionRepository;
use App\Inventory\Repository\InventoryRepository;
use Exception;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteConsumptionUseCase
{
    private ContextService $contextService;
    private ConsumptionRepository $consumptionRepository;

    public function __construct(ContextService $contextService, ConsumptionRepository $consumptionRepository)
    {
        $this->contextService = $contextService;
        $this->consumptionRepository = $consumptionRepository;
    }

    public function execute(string $consumptionId): void
    {
        /** @var Consumption $consumption */
        $consumption = $this->consumptionRepository->findOneBy(["id" => $consumptionId]);

        if (is_null($consumption)) {
            throw new NotFoundHttpException();
        }

        if (!$this->contextService->security->isGranted("DELETE", $consumption)) {
            throw new AccessDeniedHttpException();
        }

        try {
            $this->consumptionRepository->delete($consumption, true);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}
