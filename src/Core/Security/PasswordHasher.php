<?php

namespace App\Core\Security;

use SensitiveParameter;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class PasswordHasher implements PasswordHasherInterface
{
    private const CHARACTERS = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    public function hash(#[SensitiveParameter] string $plainPassword): string
    {
        $salt = $this->getSalt($plainPassword);
        $pepper = self::CHARACTERS[mt_rand(0, strlen(self::CHARACTERS) - 1)];

        return $this->getHashedPassword($salt, $plainPassword, $pepper);
    }

    public function verify(string $hashedPassword, #[SensitiveParameter] string $plainPassword): bool
    {
        $salt = $this->getSalt($plainPassword);

        for($i = 0; $i <= strlen(self::CHARACTERS) - 1; $i++) {
            $pepper = self::CHARACTERS[$i];

            if ($this->getHashedPassword($salt, $plainPassword, $pepper) === $hashedPassword) {
                return true;
            }
        }

        return false;
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }

    private function getSalt(string $password): string {
        return md5(join([$password, md5($password)]));
    }

    private function getHashedPassword(string $salt, string $plainPassword, string $pepper): string {
        return md5(join([$salt, $plainPassword, $pepper]));
    }
}