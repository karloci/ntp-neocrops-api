<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\TokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class UserAuthenticator extends AbstractAuthenticator
{
    private TokenService $tokenService;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(TokenService $tokenService, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->tokenService = $tokenService;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function supports(Request $request): ?bool
    {
        return $request->isMethod("POST");
    }

    public function authenticate(Request $request): Passport
    {
        $payload = json_decode($request->getContent(), true);

        $email = trim($payload["email"]) ?? null;
        $password = trim($payload["password"]) ?? null;

        if (!$email || !$password) {
            throw new AuthenticationException("Email or password not provided");
        }

        /** @var User $user */
        $user = $this->userRepository->findOneUserByEmail($email);
        if (is_null($user) || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException("Invalid credentials");
        }
        return new SelfValidatingPassport(new UserBadge($user->getEmail()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();

        return $this->tokenService->provideAuthenticationResponse($user);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }
}