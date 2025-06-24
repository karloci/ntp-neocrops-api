<?php

namespace App\Supply\Entity;

use App\Supply\Repository\SupplyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SupplyRepository::class)]
class Supply
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["supply:default"])]
    private ?int $id = null;

    #[ORM\Column(name: "name", type: Types::STRING, length: 45, unique: true, nullable: false)]
    #[Groups(["supply:default"])]
    private ?string $name = null;

    #[ORM\Column(name: "measure_unit", type: Types::STRING, length: 5, nullable: false)]
    #[Groups(["supply:default"])]
    private ?string $measureUnit = null;

    #[ORM\Column(name: "manufacturer", type: Types::STRING, length: 45, nullable: false)]
    #[Groups(["supply:default"])]
    private ?string $manufacturer = null;

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

    public function getMeasureUnit(): ?string
    {
        return $this->measureUnit;
    }

    public function setMeasureUnit(?string $measureUnit): void
    {
        $this->measureUnit = $measureUnit;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?string $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }
}
