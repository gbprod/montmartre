<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Wallet
{
    /** @var int */
    private $amount;

    private function __construct(int $amount)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('amount can\'t be negative');
        }

        $this->amount = $amount;
    }

    public static function containing(int $amount): self
    {
        return new self($amount);
    }

    public static function empty(): self
    {
        return new self(0);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function withAdded(int $amount): self
    {
        $self = clone $this;
        $self->amount += $amount;

        return $self; 
    }
}
