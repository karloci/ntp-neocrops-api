<?php

namespace App\Purchase\Dto;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private int $supply;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private int $amount;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private float $price;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type(type: DateTimeImmutable::class)]
    private DateTimeImmutable $date;

    #[Assert\Length(max: 45)]
    private string $invoiceNo;

    #[Assert\Length(max: 250)]

    private string $comment;

    public function getSupply(): int
    {
        return $this->supply;
    }

    public function setSupply(int $supply): void
    {
        $this->supply = $supply;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    public function getInvoiceNo(): string
    {
        return $this->invoiceNo;
    }

    public function setInvoiceNo(string $invoiceNo): void
    {
        $this->invoiceNo = $invoiceNo;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }
}
