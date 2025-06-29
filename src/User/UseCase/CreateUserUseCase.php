<?php

namespace App\User\UseCase;

use App\Core\Service\ContextService;
use App\Farm\Entity\Farm;
use App\User\Dto\UserDto;
use App\User\Entity\User;
use App\User\Exception\UniqueUserException;
use App\User\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserUseCase
{
    private ContextService $contextService;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(ContextService $contextService, UserRepository $userRepository,  UserPasswordHasherInterface $passwordHasher)
    {
        $this->contextService = $contextService;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function execute(UserDto $userDto, Farm $farm): User
    {
        if (!$this->contextService->security->isGranted("ROLE_ADMIN")) {
            throw new AccessDeniedHttpException();
        }

        if (!$this->contextService->security->isGranted("READ", $farm)) {
            throw new AccessDeniedHttpException();
        }

        try {
            $user = new User();
            $user->setFullName($userDto->getFullName());
            $user->setEmail($userDto->getEmail());
            $user->setPassword($this->passwordHasher->hashPassword($user, "1234"));
            $user->setUserFarm($farm);

            $this->userRepository->save($user, true);

            return $user;
        }
        catch (UniqueConstraintViolationException) {
            throw new UniqueUserException($this->contextService->translate("User with this email address already exists"));
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}
