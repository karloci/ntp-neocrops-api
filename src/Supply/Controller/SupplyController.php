<?php

namespace App\Supply\Controller;

use App\Core\Controller\ApiController;
use App\Supply\UseCase\FindAllSuppliesUseCase;
use App\Supply\UseCase\FindOneSupplyUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SupplyController extends ApiController
{
    #[Route("/supplies", name: "supplies_list", methods: ["GET"])]
    public function getAllSupplies(FindAllSuppliesUseCase $findAllSuppliesUseCase): JsonResponse
    {
        $supplies = $findAllSuppliesUseCase->execute();

        return $this->getHttpOkResponse($supplies, ["supply:default"]);
    }

    #[Route("/supplies/{supplyId}", name: "supplies_show", methods: ["GET"])]
    public function getOneSupply(string $supplyId, FindOneSupplyUseCase $findOneSupplyUseCase): JsonResponse
    {
        $supply = $findOneSupplyUseCase->execute($supplyId);

        return $this->getHttpOkResponse($supply, ["supply:default"]);
    }
}
