<?php

namespace App\User\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 45)]
    private string $fullName;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private string $email;

    #[Assert\NotNull]
    private bool $isAdmin;

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }
}
