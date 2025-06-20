<?php

namespace App\Profile\UseCase;

use App\Core\Service\ContextService;
use App\Core\UseCase\AbstractUseCase;
use App\Entity\User;
use App\Profile\Dto\UpdateProfileDto;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
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
        catch (ORMException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}