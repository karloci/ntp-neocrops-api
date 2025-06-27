<?php

namespace App\Inventory\UseCase;

use App\Consumption\Repository\ConsumptionRepository;
use App\Farm\Entity\Farm;
use App\Purchase\Repository\PurchaseRepository;
use App\Supply\Entity\Supply;

class FindAllSuppliesStockUseCase
{
    private PurchaseRepository $purchaseRepository;
    private ConsumptionRepository $consumptionRepository;

    public function __construct(PurchaseRepository $purchaseRepository, ConsumptionRepository $consumptionRepository)
    {
        $this->purchaseRepository = $purchaseRepository;
        $this->consumptionRepository = $consumptionRepository;
    }

    public function execute(Farm $farm): array
    {
        $purchasesBySupply = $this->purchaseRepository->findAllPurchasesByFarmGroupBySupply($farm);
        $consumptionsBySupply = $this->consumptionRepository->findAllConsumptionsByFarmGroupBySupply($farm);

        $consumptionAmounts = [];
        foreach ($consumptionsBySupply as $consumption) {
            $consumptionAmounts[$consumption["supplyId"]] = $consumption["totalAmount"];
        }

        $stockBySupply = [];
        foreach ($purchasesBySupply as $purchase) {
            $supplyId = $purchase["supplyId"];
            $consumed = $consumptionAmounts[$supplyId] ?? 0;

            $stockBySupply[] = [
                "supplyId" => $supplyId,
                "name" => $purchase["name"],
                "measureUnit" => $purchase["measureUnit"],
                "manufacturer" => $purchase["manufacturer"],
                "purchasedAmount" => $purchase["totalAmount"],
                "consumedAmount" => $consumed,
                "stock" => $purchase["totalAmount"] - $consumed,
            ];
        }

        return $stockBySupply;
    }
}