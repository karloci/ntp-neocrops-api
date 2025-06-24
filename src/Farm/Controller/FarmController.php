<?php

namespace App\Farm\Controller;

use App\Core\Controller\ApiController;
use App\Farm\Dto\FarmDto;
use App\Farm\UseCase\UpdateFarmUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class FarmController extends ApiController
{
    #[Route("/farm", name: "farm_update", methods: ["PUT"])]
    public function updateProfile(#[MapRequestPayload] FarmDto $farmDto, UpdateFarmUseCase $updateFarmUseCase): JsonResponse
    {
        $farm = $updateFarmUseCase->execute($farmDto);

        return $this->getHttpOkResponse($farm, ["farm:default"]);
    }
}