<?php

namespace App\Profile\UseCase;

use App\Core\Service\ContextService;
use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DeleteAvatarUserCase
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

        try {
            $user->setAvatar(null);

            $this->userRepository->save($user, true);

            return $user;
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}