<?php

namespace App\Inventory\Controller;

use App\Core\Controller\ApiController;
use App\Farm\Entity\Farm;
use App\Inventory\UseCase\FindSuppliesUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class InventoryController extends ApiController
{
    #[Route("/farm/{farm}/inventory/supplies", name: "inventory_supplies_list", methods: ["GET"])]
    public function getAllPurchases(Farm $farm, FindSuppliesUseCase $findAvailableSuppliesUseCase): JsonResponse
    {
        $supplies = $findAvailableSuppliesUseCase->execute($farm);

        return $this->getHttpOkResponse($supplies);
    }
}