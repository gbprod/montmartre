<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Collector
{
    private $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function paying(int $value): self
    {
        return new self($value);
    }

    public function willPay(): int
    {
        return $this->value;
    }
}
