<?php

namespace App\Core\Security;

use App\Core\Service\ContextService;
use App\Core\Service\TokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class AccessTokenAuthenticator extends AbstractAuthenticator
{
    private TokenService $tokenService;
    private UserProvider $userProvider;
    private ContextService $contextService;

    public function __construct(TokenService $tokenService, UserProvider $userProvider, ContextService $contextService)
    {
        $this->tokenService = $tokenService;
        $this->userProvider = $userProvider;
        $this->contextService = $contextService;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has("Authorization") || $request->cookies->has("accessToken");
    }

    public function authenticate(Request $request): Passport
    {
        $token = $this->tokenService->extractAccessTokenFromRequest($request);
        if (is_null($token)) {
            throw new AuthenticationException($this->contextService->translate("Access token is not provided"));
        }

        $userToken = $this->tokenService->decodeToken($token);
        if (is_null($userToken)) {
            throw new AuthenticationException($this->contextService->translate("Invalid credentials"));
        }

        $userIdentifier = $userToken->sub;
        if (!$userIdentifier) {
            throw new AuthenticationException($this->contextService->translate("Invalid credentials"));
        }

        $userBadge = new UserBadge($userIdentifier, function ($userIdentifier) {
            return $this->userProvider->loadUserByIdentifier($userIdentifier);
        });
        return new SelfValidatingPassport($userBadge);
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }
}
