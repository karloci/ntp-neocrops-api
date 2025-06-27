<?php

namespace App\Inventory\UseCase;

use App\Farm\Entity\Farm;

class FindSuppliesUseCase
{
    private FindAllStocksUseCase $findAllStocksUseCase;

    public function __construct(FindAllStocksUseCase $findAllStocksUseCase)
    {
        $this->findAllStocksUseCase = $findAllStocksUseCase;
    }

    public function execute(Farm $farm)
    {
        $inventoryStock = $this->findAllStocksUseCase->execute($farm);

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