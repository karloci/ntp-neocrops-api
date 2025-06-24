<?php

namespace App\User\UseCase;

use App\Core\Service\ContextService;
use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Exception;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteUserUseCase
{
    private ContextService $contextService;
    private UserRepository $userRepository;

    public function __construct(ContextService $contextService, UserRepository $userRepository)
    {
        $this->contextService = $contextService;
        $this->userRepository = $userRepository;
    }

    public function execute(string $userId): void
    {
        $user = $this->userRepository->findOneBy(["id" => $userId]);

        if (is_null($user)) {
            throw new NotFoundHttpException();
        }

        if (!$this->contextService->security->isGranted("DELETE", $user)) {
            throw new AccessDeniedHttpException();
        }

        try {
            $this->userRepository->delete($user, true);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}
