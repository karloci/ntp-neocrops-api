<?php

namespace App\Service;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\RefreshTokenRepository;
use App\Serializer\DataSerializer;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Random\RandomException;
use RuntimeException;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

class TokenService
{
    private string $tokenSecret;
    private Security $security;
    private RefreshTokenRepository $refreshTokenRepository;
    private DataSerializer $dataSerializer;

    public function __construct(string $tokenSecret, Security $security, RefreshTokenRepository $refreshTokenRepository, DataSerializer $dataSerializer)
    {
        $this->tokenSecret = $tokenSecret;
        $this->security = $security;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->dataSerializer = $dataSerializer;
    }

    public function revokeRefreshToken(string $token, bool $withFlush = true): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        foreach ($user->getRefreshTokens() as $refreshToken) {
            if ($refreshToken->getToken() === $token) {
                $this->refreshTokenRepository->delete($refreshToken, $withFlush);
            }
        }
    }

    public function verifyToken(string $token): bool
    {
        try {
            JWT::decode($token, new Key($this->tokenSecret, "HS256"));
        }
        catch (SignatureInvalidException|ExpiredException) {
            return false;
        }

        return true;
    }

    public function extractAccessTokenFromRequest(Request $request): string
    {
        $payload = json_decode($request->getContent(), true);

        if ($request->headers->has("Authorization")) {
            $authorizationHeader = $request->headers->get("Authorization");

            if (str_starts_with($authorizationHeader, "Bearer")) {
                return substr($authorizationHeader, 7); // strlen("Bearer ") => 7
            }
        }

        if (!is_null($payload) && array_key_exists("accessToken", $payload)) {
            return $payload["accessToken"];
        }

        throw new TokenNotFoundException();
    }

    public function extractRefreshTokenFromRequest(Request $request): string
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_null($payload) && array_key_exists("refreshToken", $payload)) {
            return $payload["refreshToken"];
        }

        throw new TokenNotFoundException();
    }

    public function provideAuthenticationResponse(User $user): JsonResponse
    {
        $accessToken = $this->generateAccessToken($user);
        $refreshToken = $this->generateRefreshToken($user);

        $data = $this->dataSerializer->serialize([
            "accessToken"  => $accessToken,
            "refreshToken" => $refreshToken,
            "user"         => $user,
            "farm"         => $user->getFarm(),
        ], ["user:default", "farm:default"]);

        return JsonResponse::fromJsonString($data, Response::HTTP_OK);
    }

    public function generateAccessToken(User $user): string
    {
        $issuedAt = new DateTime();
        $expiresAt = clone $issuedAt;
        $expiresAt->add(DateInterval::createFromDateString("+5 minutes"));
        $expiresAt = DateTimeImmutable::createFromMutable($expiresAt);
        $issuedAt = DateTimeImmutable::createFromMutable($issuedAt);

        return $this->encodeToken($user, $issuedAt, $expiresAt);
    }

    public function encodeToken(User $user, DateTimeImmutable $issuedAt, DateTimeImmutable $expiresAt): string
    {
        try {
            return JWT::encode([
                "iss" => "https://api.neocrops.com",
                "aud" => "https://app.neocrops.com",
                "sub" => $user->getUserIdentifier(),
                "iat" => $issuedAt->getTimestamp(),
                "exp" => $expiresAt->getTimestamp(),
                "jti" => bin2hex(random_bytes(16))
            ], $this->tokenSecret, "HS256");
        }
        catch (RandomException $e) {
            throw new RuntimeException("Failed to generate token identifier", 0, $e);
        }
    }

    public function generateRefreshToken(User $user): string
    {
        $issuedAt = new DateTime();
        $expiresAt = clone $issuedAt;
        $expiresAt->add(DateInterval::createFromDateString("+1 month"));
        $expiresAt = DateTimeImmutable::createFromMutable($expiresAt);
        $issuedAt = DateTimeImmutable::createFromMutable($issuedAt);

        $token = $this->encodeToken($user, $issuedAt, $expiresAt);

        $refreshToken = new RefreshToken();
        $refreshToken->setUser($user);
        $refreshToken->setToken($token);
        $refreshToken->setExpiresAt($expiresAt);
        $this->refreshTokenRepository->save($refreshToken, true);

        return $token;
    }

    public function decodeToken(string $token): stdClass
    {
        return JWT::decode($token, new Key($this->tokenSecret, "HS256"));
    }
}