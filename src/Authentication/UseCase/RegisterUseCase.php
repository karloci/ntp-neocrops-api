<?php

namespace App\Authentication\UseCase;

use App\Authentication\Dto\RegisterDto;
use App\Authentication\Exception\UniqueUserException;
use App\Core\Service\ContextService;
use App\Entity\Farm;
use App\Entity\User;
use App\Repository\FarmRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Random\RandomException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUseCase
{
    private ContextService $contextService;
    private UserRepository $userRepository;
    private FarmRepository $farmRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(ContextService $contextService, UserRepository $userRepository, FarmRepository $farmRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->contextService = $contextService;
        $this->userRepository = $userRepository;
        $this->farmRepository = $farmRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function execute(RegisterDto $registerDto): User
    {
        try {
            $farm = new Farm();
            $farm->setName($registerDto->getFarmName());
            $farm->setOib($registerDto->getFarmOib());
            $farm->setCountryIsoCode($registerDto->getFarmCountryIsoCode());
            $farm->setPostalCode($registerDto->getFarmPostalCode());
            $this->farmRepository->save($farm);

            $user = new User();
            $user->setFullName($registerDto->getFullName());
            $user->setEmail($registerDto->getEmail());
            $user->setPassword($this->passwordHasher->hashPassword($user, $registerDto->getPassword()));
            $user->setFarm($farm);

            $this->userRepository->save($user, true);

            return $user;
        }
        catch (UniqueConstraintViolationException) {
            throw new UniqueUserException($this->contextService->translate("User with this email address already exists"));
        }
        catch (RandomException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}
