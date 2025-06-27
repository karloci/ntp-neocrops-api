<?php

namespace App\Consumption\Controller;

use App\Core\Controller\ApiController;
use App\Farm\Entity\Farm;
use App\Consumption\Dto\ConsumptionDto;
use App\Consumption\UseCase\CreateConsumptionUseCase;
use App\Consumption\UseCase\DeleteConsumptionUseCase;
use App\Consumption\UseCase\FindAllConsumptionsUseCase;
use App\Consumption\UseCase\UpdateConsumptionUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class ConsumptionController extends ApiController
{
    #[Route("/farm/{farm}/consumptions", name: "consumptions_list", methods: ["GET"])]
    public function getAllConsumptions(Farm $farm, FindAllConsumptionsUseCase $findAllConsumptionsUseCase): JsonResponse
    {
        $consumptions = $findAllConsumptionsUseCase->execute($farm);

        return $this->getHttpOkResponse($consumptions, ["consumption:default", "consumption:supply"]);
    }

    #[Route("/farm/{farm}/consumptions", name: "consumptions_create", methods: ["POST"])]
    public function createConsumption(Farm $farm, #[MapRequestPayload] ConsumptionDto $consumptionDto, CreateConsumptionUseCase $createConsumptionUseCase): JsonResponse
    {
        $consumption = $createConsumptionUseCase->execute($consumptionDto, $farm);

        return $this->getHttpCreatedResponse($consumption, ["consumption:default", "consumption:supply"]);
    }

    #[Route("/farm/{farm}/consumptions/{consumptionId}", name: "consumptions_update", methods: ["PUT"])]
    public function updateConsumption(string $consumptionId, #[MapRequestPayload] ConsumptionDto $consumptionDto, UpdateConsumptionUseCase $updateConsumptionUseCase): JsonResponse
    {
        $consumption = $updateConsumptionUseCase->execute($consumptionId, $consumptionDto);

        return $this->getHttpOkResponse($consumption, ["consumption:default", "consumption:supply"]);
    }

    #[Route("/farm/{farm}/consumptions/{consumptionId}", name: "consumptions_delete", methods: ["DELETE"])]
    public function deleteConsumption(string $consumptionId, DeleteConsumptionUseCase $deleteConsumptionUseCase): JsonResponse
    {
        $deleteConsumptionUseCase->execute($consumptionId);

        return $this->getHttpNoContentResponse();
    }
}