<?php

namespace App\Consumption\Entity;
;

use App\Farm\Entity\Farm;
use App\Purchase\Repository\PurchaseRepository;
use App\Supply\Entity\Supply;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Consumption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["consumption:default"])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "supply_id", referencedColumnName: "id", unique: false, nullable: false)]
    #[Groups(["consumption:supply"])]
    private ?Supply $supply = null;

    #[ORM\Column(name: "amount", type: Types::FLOAT, nullable: false)]
    #[Groups(["consumption:amount"])]
    private ?float $amount = null;

    #[ORM\Column(name: "transaction_date", type: Types::DATE_IMMUTABLE, unique: false, nullable: false)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => "Y-m-d"])]
    #[Groups(["consumption:default"])]
    private ?DateTimeImmutable $date = null;

    #[ORM\Column(name: "comment", type: Types::TEXT, length: 45, nullable: true)]
    #[Groups(["consumption:default"])]
    private ?string $comment = null;


    #[ORM\ManyToOne(inversedBy: "consumptions")]
    #[ORM\JoinColumn(name: "farm_id", referencedColumnName: "id", unique: false, nullable: false)]
    #[Groups(["consumption:farm"])]
    private ?Farm $farm = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSupply(): ?Supply
    {
        return $this->supply;
    }

    public function setSupply(?Supply $supply): void
    {
        $this->supply = $supply;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getFarm(): ?Farm
    {
        return $this->farm;
    }

    public function setFarm(?Farm $farm): void
    {
        $this->farm = $farm;
    }
}