<?php

namespace App\Purchase\UseCase;

use App\Core\Service\ContextService;
use App\Farm\Entity\Farm;
use App\Purchase\Entity\Purchase;
use App\Purchase\Repository\PurchaseRepository;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;

class FindAllPurchasesUseCase
{
    private ContextService $contextService;
    private PurchaseRepository $purchaseRepository;

    public function __construct(ContextService $contextService, PurchaseRepository $purchaseRepository)
    {
        $this->contextService = $contextService;
        $this->purchaseRepository = $purchaseRepository;
    }

    /**
     * @return Purchase[]
     */
    public function execute(Farm $farm): array
    {
        $countries = $this->purchaseRepository->findAllPurchasesByFarm($farm);

        $result = [];
        foreach ($countries as $purchase) {
            if ($this->contextService->security->isGranted("READ", $purchase)) {
                $result[] = $purchase;
            }
        }

        return $result;
    }
}