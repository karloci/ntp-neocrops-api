<?php

namespace App\Profile\UseCase;

use App\Core\Service\ContextService;
use App\Profile\Dto\UpdateProfileDto;
use App\User\Entity\User;
use App\User\Exception\UniqueUserException;
use App\User\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GetAvatarUserCase
{
    private ContextService $contextService;
    private UserRepository $userRepository;

    public function __construct(ContextService $contextService, UserRepository $userRepository)
    {
        $this->contextService = $contextService;
        $this->userRepository = $userRepository;
    }

    public function execute()
    {
        /** @var User $user */
        $user = $this->contextService->security->getUser();

        $avatarData = $user->getAvatar();

        if (!$avatarData) {
            return null;
        }

        return $avatarData;
    }
}