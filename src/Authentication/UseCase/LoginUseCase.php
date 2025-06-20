<?php

namespace App\Authentication\UseCase;

use App\Authentication\Dto\LoginDto;
use App\Core\Service\ContextService;
use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginUseCase
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

    public function execute(LoginDto $loginDto): User
    {
        $email = $loginDto->getEmail();
        $password = $loginDto->getPassword();

        try {
            $user = $this->userRepository->findOneUserByEmail($email);

            if (is_null($user) || !$this->passwordHasher->isPasswordValid($user, $password)) {
                throw new AuthenticationException($this->contextService->translate("Invalid credentials"));
            }

            return $user;
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}