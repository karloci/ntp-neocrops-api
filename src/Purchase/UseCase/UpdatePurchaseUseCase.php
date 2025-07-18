<?php

namespace App\Purchase\UseCase;

use App\Consumption\Exception\InvalidConsumptionException;
use App\Core\Service\ContextService;
use App\Inventory\Repository\InventoryRepository;
use App\Purchase\Dto\PurchaseDto;
use App\Purchase\Entity\Purchase;
use App\Purchase\Repository\PurchaseRepository;
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

class UpdatePurchaseUseCase
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

    public function execute(string $purchaseId, PurchaseDto $purchaseDto): Purchase
    {
        /** @var Purchase $purchase */
        $purchase = $this->purchaseRepository->findOneBy(["id" => $purchaseId]);

        if (is_null($purchase)) {
            throw new NotFoundHttpException();
        }

        if (!$this->contextService->security->isGranted("UPDATE", $purchase)) {
            throw new AccessDeniedHttpException();
        }

        $currentStock = $this->inventoryRepository->findStockForSupply($purchase->getFarm(), $purchaseDto->getSupply());

        if (($purchase->getAmount() - $purchaseDto->getAmount()) > $currentStock) {
            throw new InvalidConsumptionException($this->contextService->translate("Updating is not possible because there will not be enough stock remaining"));
        }

        try {
            $purchase->setSupply($this->contextService->entityManager->getReference(Supply::class, $purchaseDto->getSupply()));
            $purchase->setAmount($purchaseDto->getAmount());
            $purchase->setPrice($purchaseDto->getPrice());
            $purchase->setDate($purchaseDto->getDate());
            $purchase->setInvoiceNo($purchaseDto->getInvoiceNo());
            $purchase->setComment($purchaseDto->getComment());

            $this->purchaseRepository->save($purchase, true);
        }
        catch (ORMException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $purchase;
    }
}
