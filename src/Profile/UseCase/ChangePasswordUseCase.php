<?php

namespace App\Profile\UseCase;

use App\Core\Service\ContextService;
use App\Entity\User;
use App\Profile\Dto\ChangePasswordDto;
use App\Repository\UserRepository;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordUseCase
{
    private ContextService $contextService;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(ContextService $contextService, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->contextService = $contextService;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function execute(ChangePasswordDto $changePasswordDto): void
    {
        /** @var User $user */
        $user = $this->contextService->security->getUser();

        try {
            $user->setPassword($this->passwordHasher->hashPassword($user, $changePasswordDto->getNewPassword()));

            $this->userRepository->save($user, true);
        }
        catch (ORMException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}