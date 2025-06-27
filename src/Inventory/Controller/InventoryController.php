<?php

namespace App\Inventory\Controller;

use App\Core\Controller\ApiController;
use App\Farm\Entity\Farm;
use App\Inventory\UseCase\FindAvailableSuppliesUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class InventoryController extends ApiController
{
    #[Route("/farm/{farm}/inventory/supplies/available", name: "supplies_available_list", methods: ["GET"])]
    public function getAllPurchases(Farm $farm, FindAvailableSuppliesUseCase $findAvailableSuppliesUseCase): JsonResponse
    {
        $supplies = $findAvailableSuppliesUseCase->execute($farm);

        return $this->getHttpOkResponse($supplies);
    }
}