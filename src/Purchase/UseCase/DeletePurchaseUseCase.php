<?php

namespace App\Purchase\UseCase;

use App\Consumption\Exception\InvalidConsumptionException;
use App\Core\Service\ContextService;
use App\Inventory\Repository\InventoryRepository;
use App\Purchase\Entity\Purchase;
use App\Purchase\Repository\PurchaseRepository;
use Exception;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeletePurchaseUseCase
{
    private ContextService $contextService;
    private PurchaseRepository $purchaseRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(ContextService $contextService, PurchaseRepository $purchaseRepository, InventoryRepository $inventoryRepository)
    {
        $this->contextService = $contextService;
        $this->purchaseRepository = $purchaseRepository;
        $this->inventoryRepository = $inventoryRepository;
    }

    public function execute(string $purchaseId): void
    {
        /** @var Purchase $purchase */
        $purchase = $this->purchaseRepository->findOneBy(["id" => $purchaseId]);

        if (is_null($purchase)) {
            throw new NotFoundHttpException();
        }

        if (!$this->contextService->security->isGranted("DELETE", $purchase)) {
            throw new AccessDeniedHttpException();
        }


        $currentStock = $this->inventoryRepository->findStockForSupply($purchase->getFarm(), $purchase->getSupply());
        if ($purchase->getAmount() > $currentStock) {
            throw new InvalidConsumptionException("Neće biti dovoljno na lageru!");
        }

        try {
            $this->purchaseRepository->delete($purchase, true);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}
