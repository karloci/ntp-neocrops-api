<?php

namespace App\User\UseCase;

use App\Core\Service\ContextService;
use App\Farm\Entity\Farm;
use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;

class FindAllUsersUseCase
{
    private ContextService $contextService;
    private UserRepository $userRepository;

    public function __construct(ContextService $contextService, UserRepository $userRepository)
    {
        $this->contextService = $contextService;
        $this->userRepository = $userRepository;
    }

    /**
     * @return User[]
     */
    public function execute(Farm $farm): array
    {
        try {
            $countries = $this->userRepository->findAllUsersByFarm($farm);

            $result = [];
            foreach ($countries as $user) {
                if ($this->contextService->security->isGranted("READ", $user)) {
                    $result[] = $user;
                }
            }

            return $result;
        }
        catch (InvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }
}
