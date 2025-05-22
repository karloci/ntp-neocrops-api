<?php

namespace App\Profile\UseCase;

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

class UpdateProfileUseCase extends AbstractUseCase
{
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, Security $security, UserRepository $userRepository)
    {
        parent::__construct($entityManager, $security);

        $this->userRepository = $userRepository;
    }

    public function execute(UpdateProfileDto $updateProfileDto): User
    {
        /** @var User $user */
        $user = $this->security->getUser();

        try {
            $user->setFullName($updateProfileDto->getFullName());

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