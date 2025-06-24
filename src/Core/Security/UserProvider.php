<?php

namespace App\Core\Security;

use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        /** @var User|null $user */
        $user = $this->userRepository->loadUserByIdentifier($identifier);

        if (is_null($user)) {
            throw new UserNotFoundException("User not found");
        }

        return $user;
    }
}
