<?php

namespace App\Authentication\Controller;

use App\Authentication\Dto\LoginDto;
use App\Authentication\UseCase\LoginUseCase;
use App\Authentication\UseCase\RefreshTokenUseCase;
use App\Core\Controller\ApiController;
use App\Core\Service\TokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class AuthenticationController extends ApiController
{
    #[Route("/authentication/login", name: "authentication_login", methods: ["POST"])]
    public function login(#[MapRequestPayload] LoginDto $loginDto, LoginUseCase $loginUseCase, TokenService $tokenService): JsonResponse
    {
        $user = $loginUseCase->execute($loginDto);

        return $tokenService->provideAuthenticationResponse($user);
    }

    #[Route("/authentication/refresh-token", name: "authentication_refresh_token", methods: ["POST"])]
    public function refreshToken(Request $request, RefreshTokenUseCase $refreshTokenUseCase, TokenService $tokenService): JsonResponse
    {
        $user = $refreshTokenUseCase->execute($request);

        return $tokenService->provideAuthenticationResponse($user);
    }
}