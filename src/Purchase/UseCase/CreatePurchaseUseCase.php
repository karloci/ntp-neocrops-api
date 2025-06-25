<?php

namespace App\Purchase\UseCase;

use App\Core\Service\ContextService;
use App\Farm\Entity\Farm;
use App\Purchase\Dto\PurchaseDto;
use App\Purchase\Entity\Purchase;
use App\Purchase\Repository\PurchaseRepository;
use App\Supply\Entity\Supply;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CreatePurchaseUseCase
{
    private ContextService $contextService;
    private PurchaseRepository $purchaseRepository;

    public function __construct(ContextService $contextService, PurchaseRepository $purchaseRepository)
    {
        $this->contextService = $contextService;
        $this->purchaseRepository = $purchaseRepository;
    }

    public function execute(PurchaseDto $purchaseDto, Farm $farm): Purchase
    {
        if (!$this->contextService->security->isGranted("READ", $farm)) {
            throw new AccessDeniedHttpException();
        }

        try {
            $purchase = new Purchase();
            $purchase->setSupply($this->contextService->entityManager->getReference(Supply::class, $purchaseDto->getSupply()));
            $purchase->setAmount($purchaseDto->getAmount());
            $purchase->setPrice($purchaseDto->getPrice());
            $purchase->setDate($purchaseDto->getDate());
            $purchase->setInvoiceNo($purchaseDto->getInvoiceNo());
            $purchase->setComment($purchaseDto->getComment());
            $purchase->setFarm($farm);

            $this->purchaseRepository->save($purchase, true);

            return $purchase;
        }
        catch (ORMException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}
