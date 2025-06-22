<?php

namespace App\Authentication\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegisterDto
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

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 8, max: 64)]
    private string $password;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 64)]
    private string $repeatPassword;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 45)]
    private string $farmName;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 11)]
    private string $farmOib;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 2)]
    private string $farmCountryIsoCode;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 5)]
    private string $farmPostalCode;

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->getPassword() !== $this->getRepeatPassword()) {
            $context->buildViolation("Repeat password do not match!")
                ->atPath("repeatPassword")
                ->addViolation();
        }
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getRepeatPassword(): ?string
    {
        return $this->repeatPassword;
    }

    public function setRepeatPassword(?string $repeatPassword): void
    {
        $this->repeatPassword = $repeatPassword;
    }

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

    public function getFarmName(): string
    {
        return $this->farmName;
    }

    public function setFarmName(string $farmName): void
    {
        $this->farmName = $farmName;
    }

    public function getFarmOib(): string
    {
        return $this->farmOib;
    }

    public function setFarmOib(string $farmOib): void
    {
        $this->farmOib = $farmOib;
    }

    public function getFarmCountryIsoCode(): string
    {
        return $this->farmCountryIsoCode;
    }

    public function setFarmCountryIsoCode(string $farmCountryIsoCode): void
    {
        $this->farmCountryIsoCode = $farmCountryIsoCode;
    }

    public function getFarmPostalCode(): string
    {
        return $this->farmPostalCode;
    }

    public function setFarmPostalCode(string $farmPostalCode): void
    {
        $this->farmPostalCode = $farmPostalCode;
    }
}
