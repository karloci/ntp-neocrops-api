<?php

namespace App\Authentication\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LoginDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private string $email;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    private string $password;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }
}