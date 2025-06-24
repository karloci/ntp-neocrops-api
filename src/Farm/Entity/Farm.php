<?php

namespace App\Farm\Entity;

use App\Consumption\Entity\Consumption;
use App\Cultivation\Entity\Cultivation;
use App\Farm\Repository\FarmRepository;
use App\Purchase\Entity\Purchase;
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


    /**
     * @var Collection<int, Purchase>
     */
    #[ORM\OneToMany(targetEntity: Purchase::class, mappedBy: "farm", orphanRemoval: true)]
    private Collection $purchases;

    /**
     * @var Collection<int, Consumption>
     */
    #[ORM\OneToMany(targetEntity: Consumption::class, mappedBy: "farm", orphanRemoval: true)]
    private Collection $consumptions;

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

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): static
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setFarm($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): static
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getFarm() === $this) {
                $purchase->setFarm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Consumption>
     */
    public function getConsumptions(): Collection
    {
        return $this->consumptions;
    }

    public function addConsumption(Consumption $consumption): static
    {
        if (!$this->consumptions->contains($consumption)) {
            $this->consumptions->add($consumption);
            $consumption->setFarm($this);
        }

        return $this;
    }

    public function removeConsumption(Consumption $consumption): static
    {
        if ($this->consumptions->removeElement($consumption)) {
            // set the owning side to null (unless already changed)
            if ($consumption->getFarm() === $this) {
                $consumption->setFarm(null);
            }
        }

        return $this;
    }
}
