<?php

namespace App\Entity;

use App\Repository\FarmRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FarmRepository::class)]
class Farm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["farm:default", "user:farm"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["farm:default", "user:farm"])]
    private ?string $name = null;

    #[ORM\Column(length: 11, unique: true)]
    #[Groups(["farm:default", "user:farm"])]
    private ?string $oib = null;

    #[ORM\Column(length: 2)]
    #[Groups(["farm:default", "user:farm"])]
    private ?string $countryIsoCode = null;

    #[ORM\Column(length: 5)]
    #[Groups(["farm:default", "user:farm"])]
    private ?string $postalCode = null;

    #[ORM\OneToOne(mappedBy: "userFarm", cascade: ["persist", "remove"])]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOib(): ?string
    {
        return $this->oib;
    }

    public function setOib(string $oib): static
    {
        $this->oib = $oib;

        return $this;
    }

    public function getCountryIsoCode(): ?string
    {
        return $this->countryIsoCode;
    }

    public function setCountryIsoCode(string $countryIsoCode): static
    {
        $this->countryIsoCode = $countryIsoCode;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        // set the owning side of the relation if necessary
        if ($owner->getUserFarm() !== $this) {
            $owner->setUserFarm($this);
        }

        $this->owner = $owner;

        return $this;
    }
}
