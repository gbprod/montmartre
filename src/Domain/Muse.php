<?php

namespace GBProd\Montmartre\Domain;

final class Muse
{
    private $color;
    private $value;

    private function __construct(Color $color, int $value)
    {
        $this->color = $color;
        $this->value = $value;
    }

    public static function painted(Color $solor, int $value): self
    {
        return new self($solor, $value);
    }

    public function color(): Color
    {
        return $this->color;
    }

    public function value(): int
    {
        return $this->value;
    }
}
