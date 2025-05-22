<?php

namespace App\Profile\Dto;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ChangePasswordDto
{

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[SecurityAssert\UserPassword]
    private string $currentPassword;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 8, max: 64)]
    private string $newPassword;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 64)]
    private string $repeatPassword;

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->getNewPassword() !== $this->getRepeatPassword()) {
            $context->buildViolation("Repeat password do not match!")
                ->atPath("repeatPassword")
                ->addViolation();
        }
    }

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(string $currentPassword): void
    {
        $this->currentPassword = $currentPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getRepeatPassword(): ?string
    {
        return $this->repeatPassword;
    }

    public function setRepeatPassword(?string $repeatPassword): void
    {
        $this->repeatPassword = $repeatPassword;
    }
}