<?php

namespace App\Core\Service;

use App\Authentication\Entity\RefreshToken;
use App\Authentication\Repository\RefreshTokenRepository;
use App\Core\Serializer\DataSerializer;
use App\User\Entity\User;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Random\RandomException;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenService
{
    private string $encryptionSecret;
    private string $signatureSecret;
    private Security $security;
    private RefreshTokenRepository $refreshTokenRepository;
    private DataSerializer $dataSerializer;

    public function __construct(string $encryptionSecret, string $signatureSecret, Security $security, RefreshTokenRepository $refreshTokenRepository, DataSerializer $dataSerializer)
    {
        $this->encryptionSecret = $encryptionSecret;
        $this->signatureSecret = $signatureSecret;
        $this->security = $security;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->dataSerializer = $dataSerializer;
    }

    public function revokeRefreshToken(string $token, bool $withFlush = true): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if (!is_null($user)) {
            foreach ($user->getRefreshTokens() as $refreshToken) {
                if ($refreshToken->getToken() === $token) {
                    $this->refreshTokenRepository->delete($refreshToken, $withFlush);
                }
            }
        }
    }

    public function extractAccessTokenFromRequest(Request $request): ?string
    {
        if ($request->headers->has("Authorization")) {
            $authorizationHeader = $request->headers->get("Authorization");

            if (str_starts_with($authorizationHeader, "Bearer")) {
                return substr($authorizationHeader, 7); // strlen("Bearer ") => 7
            }
        }

        if ($request->cookies->has("accessToken")) {
            return $request->cookies->get("accessToken");
        }

        return null;
    }

    public function extractRefreshTokenFromRequest(Request $request): ?string
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_null($payload) && array_key_exists("refreshToken", $payload)) {
            return $payload["refreshToken"];
        }

        if ($request->cookies->has("refreshToken")) {
            return $request->cookies->get("refreshToken");
        }

        return null;
    }

    public function provideAuthenticationResponse(User $user): JsonResponse
    {
        $accessToken = $this->generateAccessToken($user);
        $refreshToken = $this->generateRefreshToken($user);

        $data = $this->dataSerializer->serialize([
            "accessToken"  => $accessToken,
            "refreshToken" => $refreshToken,
            "user"         => $user
        ], ["user:default", "user:farm", "farm:default"]);

        $response = JsonResponse::fromJsonString($data, Response::HTTP_OK);

        $response->headers->setCookie($this->getAccessTokenCookie($accessToken));
        $response->headers->setCookie($this->getRefreshTokenCookie($refreshToken));

        return $response;
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
            $payload = [
                "sub" => $user->getUserIdentifier(),
                "iat" => $issuedAt->getTimestamp(),
                "exp" => $expiresAt->getTimestamp(),
                "jti" => bin2hex(random_bytes(16))
            ];
            return $this->encrypt($payload);
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

    public function getAccessTokenCookie(?string $token): Cookie
    {
        $expiration = is_null($token) ? time() : $this->decodeToken($token)?->exp;

        return new Cookie(
            name: "accessToken",
            value: $token,
            expire: $expiration,
            secure: true,
            sameSite: Cookie::SAMESITE_NONE
        );
    }

    public function decodeToken(string $token): ?object
    {
        $data = $this->decrypt($token);
        if (!$data || !isset($data["exp"]) || $data["exp"] < time()) {
            return null;
        }

        return json_decode(json_encode($data));
    }

    public function getRefreshTokenCookie(?string $token): Cookie
    {
        $expiration = is_null($token) ? time() : $this->decodeToken($token)?->exp;

        return new Cookie(
            name: "refreshToken",
            value: $token,
            expire: $expiration,
            secure: true,
            sameSite: Cookie::SAMESITE_NONE
        );
    }

    private function encrypt(array $payload): string
    {
        $iv = random_bytes(16);
        $data = json_encode($payload);
        $cipherText = openssl_encrypt($data, "aes-256-cbc", $this->encryptionSecret, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac("sha256", $iv . $cipherText, $this->signatureSecret, true);

        return base64_encode($iv . $hmac . $cipherText);
    }

    private function decrypt(string $token): ?array
    {
        $decoded = base64_decode($token);
        if (strlen($decoded) < 48) { // 16 iv + 32 hmac = 48 B
            return null;
        }

        $iv = substr($decoded, 0, 16);
        $hmac = substr($decoded, 16, 32);
        $cipherText = substr($decoded, 48);

        $calculatedHmac = hash_hmac("sha256", $iv . $cipherText, $this->signatureSecret, true);
        if (!hash_equals($hmac, $calculatedHmac)) {
            return null;
        }

        $json = openssl_decrypt($cipherText, "aes-256-cbc", $this->encryptionSecret, OPENSSL_RAW_DATA, $iv);
        return json_decode($json, true);
    }
}
