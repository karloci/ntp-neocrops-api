<?php

namespace App\Authentication\UseCase;

use App\Core\Service\ContextService;
use App\Core\Service\TokenService;
use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class RefreshTokenUseCase
{
    private ContextService $contextService;
    private UserRepository $userRepository;
    private TokenService $tokenService;

    public function __construct(ContextService $contextService, UserRepository $userRepository, TokenService $tokenService)
    {
        $this->contextService = $contextService;
        $this->userRepository = $userRepository;
        $this->tokenService = $tokenService;
    }

    public function execute(Request $request): User
    {
        $token = $this->tokenService->extractRefreshTokenFromRequest($request);
        if (is_null($token)) {
            throw new AuthenticationException($this->contextService->translate("Refresh token is not provided"));
        }

        $userToken = $this->tokenService->decodeToken($token);
        if (is_null($userToken)) {
            throw new AuthenticationException($this->contextService->translate("Invalid credentials"));
        }

        try {
            $userIdentifier = $userToken->sub;

            $user = $this->userRepository->loadUserByIdentifierAndRefreshToken($userIdentifier, $token);

            if (is_null($user)) {
                throw new AuthenticationException($this->contextService->translate($this->contextService->translate("Invalid credentials")));
            }

            return $user;
        }
        catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }
    }
}