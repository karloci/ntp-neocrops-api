<?php

namespace App\Purchase\Controller;

use App\Core\Controller\ApiController;
use App\Farm\Entity\Farm;
use App\Purchase\Dto\PurchaseDto;
use App\Purchase\UseCase\CreatePurchaseUseCase;
use App\Purchase\UseCase\DeletePurchaseUseCase;
use App\Purchase\UseCase\FindAllPurchasesUseCase;
use App\Purchase\UseCase\UpdatePurchaseUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class PurchaseController extends ApiController
{
    #[Route("/farm/{farm}/purchases", name: "purchases_list", methods: ["GET"])]
    public function getAllPurchases(Farm $farm, FindAllPurchasesUseCase $findAllPurchasesUseCase): JsonResponse
    {
        $purchases = $findAllPurchasesUseCase->execute($farm);

        return $this->getHttpOkResponse($purchases, ["purchase:default", "purchase:supply"]);
    }

    #[Route("/farm/{farm}/purchases", name: "purchases_create", methods: ["POST"])]
    public function createPurchase(Farm $farm, #[MapRequestPayload] PurchaseDto $purchaseDto, CreatePurchaseUseCase $createPurchaseUseCase): JsonResponse
    {
        $purchase = $createPurchaseUseCase->execute($purchaseDto, $farm);

        return $this->getHttpCreatedResponse($purchase, ["purchase:default", "purchase:supply"]);
    }

    #[Route("/farm/{farm}/purchases/{purchaseId}", name: "purchases_update", methods: ["PUT"])]
    public function updatePurchase(string $purchaseId, #[MapRequestPayload] PurchaseDto $purchaseDto, UpdatePurchaseUseCase $updatePurchaseUseCase): JsonResponse
    {
        $purchase = $updatePurchaseUseCase->execute($purchaseId, $purchaseDto);

        return $this->getHttpOkResponse($purchase, ["purchase:default", "purchase:supply"]);
    }

    #[Route("/farm/{farm}/purchases/{purchaseId}", name: "purchases_delete", methods: ["DELETE"])]
    public function deletePurchase(string $purchaseId, DeletePurchaseUseCase $deletePurchaseUseCase): JsonResponse
    {
        $deletePurchaseUseCase->execute($purchaseId);

        return $this->getHttpNoContentResponse();
    }
}