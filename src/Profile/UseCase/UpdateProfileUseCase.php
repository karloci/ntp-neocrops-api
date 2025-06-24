<?php

namespace App\Profile\UseCase;

use App\Authentication\Exception\UniqueUserException;
use App\Core\Service\ContextService;
use App\Profile\Dto\UpdateProfileDto;
use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateProfileUseCase
{
    private ContextService $contextService;
    private UserRepository $userRepository;

    public function __construct(ContextService $contextService, UserRepository $userRepository)
    {
        $this->contextService = $contextService;
        $this->userRepository = $userRepository;
    }

    public function execute(UpdateProfileDto $updateProfileDto): User
    {
        /** @var User $user */
        $user = $this->contextService->security->getUser();

        try {
            $user->setFullName($updateProfileDto->getFullName());
            $user->setEmail($updateProfileDto->getEmail());

            $this->userRepository->save($user, true);

            return $user;
        }
        catch (UniqueConstraintViolationException) {
            throw new UniqueUserException($this->contextService->translate("User with this email address already exists"));
        }
        catch (ORMException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}