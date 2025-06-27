<?php

namespace App\Inventory\UseCase;

use App\Farm\Entity\Farm;

class FindAvailableSuppliesUseCase
{
    private FindAllSuppliesStockUseCase $findAllSuppliesStockUseCase;

    public function __construct(FindAllSuppliesStockUseCase $findAllSuppliesStockUseCase)
    {
        $this->findAllSuppliesStockUseCase = $findAllSuppliesStockUseCase;
    }

    public function execute(Farm $farm)
    {
        $inventoryStock = $this->findAllSuppliesStockUseCase->execute($farm);

        $result = [];
        foreach ($inventoryStock as $stock) {
            $result[] = [
                "id" => $stock["supplyId"],
                "name" => $stock["name"],
                "manufacturer" => $stock["manufacturer"],
                "measureUnit" => $stock["measureUnit"],
            ];
        }

        return $result;
    }
}