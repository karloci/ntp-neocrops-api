<?php

namespace App\Farm\Entity;

use App\Farm\Repository\FarmRepository;
use App\User\Entity\User;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: "userFarm")]
    private Collection $users;

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setUserFarm($this);
        }

        return $this;
    }
}
