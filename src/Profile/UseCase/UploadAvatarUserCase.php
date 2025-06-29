<?php

namespace App\Profile\UseCase;

use App\Core\Service\ContextService;
use App\User\Entity\User;
use App\User\Exception\UniqueUserException;
use App\User\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UploadAvatarUserCase
{
    private ContextService $contextService;
    private UserRepository $userRepository;

    public function __construct(ContextService $contextService, UserRepository $userRepository)
    {
        $this->contextService = $contextService;
        $this->userRepository = $userRepository;
    }

    public function execute(Request $request)
    {
        /** @var User $user */
        $user = $this->contextService->security->getUser();

        $imageData = $request->getContent();

        if (!$imageData) {
            throw new BadRequestHttpException($this->contextService->translate("No image data received"));
        }


        try {
            $user->setAvatar($imageData);

            $this->userRepository->save($user, true);

            return $user;
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}