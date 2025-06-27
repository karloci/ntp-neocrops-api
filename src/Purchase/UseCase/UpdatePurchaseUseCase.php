<?php

namespace App\Purchase\UseCase;

use App\Core\Service\ContextService;
use App\Purchase\Dto\PurchaseDto;
use App\Purchase\Entity\Purchase;
use App\Purchase\Repository\PurchaseRepository;
use App\Supply\Entity\Supply;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdatePurchaseUseCase
{
    private ContextService $contextService;
    private PurchaseRepository $purchaseRepository;

    public function __construct(ContextService $contextService, PurchaseRepository $purchaseRepository)
    {
        $this->contextService = $contextService;
        $this->purchaseRepository = $purchaseRepository;
    }

    public function execute(string $purchaseId, PurchaseDto $purchaseDto): Purchase
    {
        $purchase = $this->purchaseRepository->findOneBy(["id" => $purchaseId]);

        if (is_null($purchase)) {
            throw new NotFoundHttpException();
        }

        if (!$this->contextService->security->isGranted("UPDATE", $purchase)) {
            throw new AccessDeniedHttpException();
        }

        // TODO: provjera je li ima dovoljno na lageru

        try {
            $purchase->setSupply($this->contextService->entityManager->getReference(Supply::class, $purchaseDto->getSupply()));
            $purchase->setAmount($purchaseDto->getAmount());
            $purchase->setPrice($purchaseDto->getPrice());
            $purchase->setDate($purchaseDto->getDate());
            $purchase->setInvoiceNo($purchaseDto->getInvoiceNo());
            $purchase->setComment($purchaseDto->getComment());

            $this->purchaseRepository->save($purchase, true);
        }
        catch (ORMException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $purchase;
    }
}
