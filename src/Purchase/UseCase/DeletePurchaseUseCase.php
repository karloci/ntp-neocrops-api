<?php

namespace App\Purchase\UseCase;

use App\Core\Service\ContextService;
use App\Purchase\Entity\Purchase;
use App\Purchase\Repository\PurchaseRepository;
use Exception;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeletePurchaseUseCase
{
    private ContextService $contextService;
    private PurchaseRepository $purchaseRepository;

    public function __construct(ContextService $contextService, PurchaseRepository $purchaseRepository)
    {
        $this->contextService = $contextService;
        $this->purchaseRepository = $purchaseRepository;
    }

    public function execute(string $purchaseId): void
    {
        $purchase = $this->purchaseRepository->findOneBy(["id" => $purchaseId]);

        if (is_null($purchase)) {
            throw new NotFoundHttpException();
        }

        if (!$this->contextService->security->isGranted("DELETE", $purchase)) {
            throw new AccessDeniedHttpException();
        }

        try {
            $this->purchaseRepository->delete($purchase, true);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}
