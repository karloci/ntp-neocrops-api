<?php

namespace App\User\UseCase;

use App\Core\Service\ContextService;
use App\User\Dto\UserDto;
use App\User\Entity\User;
use App\User\Exception\UniqueUserException;
use App\User\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateUserUseCase
{
    private ContextService $contextService;
    private UserRepository $userRepository;

    public function __construct(ContextService $contextService, UserRepository $userRepository)
    {
        $this->contextService = $contextService;
        $this->userRepository = $userRepository;
    }

    public function execute(string $userId, UserDto $userDto): User
    {
        $user = $this->userRepository->findOneBy(["id" => $userId]);

        if (is_null($user)) {
            throw new NotFoundHttpException();
        }

        if (!$this->contextService->security->isGranted("UPDATE", $user)) {
            throw new AccessDeniedHttpException();
        }

        try {
            $user->setFullName($userDto->getFullName());
            $user->setEmail($userDto->getEmail());

            $this->userRepository->save($user, true);
        }
        catch (UniqueConstraintViolationException) {
            throw new UniqueUserException($this->contextService->translate("User with this email address already exists"));
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $user;
    }
}
