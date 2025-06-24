<?php

namespace App\Supply\UseCase;

use App\Core\Service\ContextService;
use App\Supply\Entity\Supply;
use App\Supply\Repository\SupplyRepository;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;

class FindAllSuppliesUseCase
{
    private SupplyRepository $supplyRepository;

    public function __construct(SupplyRepository $supplyRepository)
    {
        $this->supplyRepository = $supplyRepository;
    }

    /**
     * @return Supply[]
     */
    public function execute(): array
    {
        return $this->supplyRepository->findAllSupplies();
    }
}
