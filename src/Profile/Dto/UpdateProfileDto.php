<?php

namespace App\Profile\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateProfileDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    private string $fullName;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private string $email;

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
}