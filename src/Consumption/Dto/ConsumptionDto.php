<?php

namespace App\Consumption\Dto;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class ConsumptionDto
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
    #[Assert\Type(type: DateTimeImmutable::class)]
    private DateTimeImmutable $date;

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

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
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
