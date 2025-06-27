<?php

namespace App\Consumption\UseCase;

use App\Core\Service\ContextService;
use App\Farm\Entity\Farm;
use App\Consumption\Entity\Consumption;
use App\Consumption\Repository\ConsumptionRepository;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;

class FindAllConsumptionsUseCase
{
    private ContextService $contextService;
    private ConsumptionRepository $consumptionRepository;

    public function __construct(ContextService $contextService, ConsumptionRepository $consumptionRepository)
    {
        $this->contextService = $contextService;
        $this->consumptionRepository = $consumptionRepository;
    }

    /**
     * @return Consumption[]
     */
    public function execute(Farm $farm): array
    {
        $countries = $this->consumptionRepository->findAllConsumptionsByFarm($farm);

        $result = [];
        foreach ($countries as $consumption) {
            if ($this->contextService->security->isGranted("READ", $consumption)) {
                $result[] = $consumption;
            }
        }

        return $result;
    }
}