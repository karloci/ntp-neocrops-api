<?php

namespace App\Farm\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class FarmDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 45)]
    private string $name;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 11)]
    private string $oib;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 2)]
    private string $countryIsoCode;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(exactly: 5)]
    private string $postalCode;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOib(): string
    {
        return $this->oib;
    }

    public function setOib(string $oib): void
    {
        $this->oib = $oib;
    }

    public function getCountryIsoCode(): string
    {
        return $this->countryIsoCode;
    }

    public function setCountryIsoCode(string $countryIsoCode): void
    {
        $this->countryIsoCode = $countryIsoCode;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }
}