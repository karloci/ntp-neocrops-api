<?php

namespace App\Supply\UseCase;

use App\Supply\Entity\Supply;
use App\Supply\Repository\SupplyRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FindOneSupplyUseCase
{
    private SupplyRepository $supplyRepository;

    public function __construct(SupplyRepository $supplyRepository)
    {
        $this->supplyRepository = $supplyRepository;
    }

    public function execute(string $supplyId): Supply
    {
        $supply = $this->supplyRepository->findOneSupply($supplyId);

        if (is_null($supply)) {
            throw new NotFoundHttpException();
        }

        return $supply;
    }
}
