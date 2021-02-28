<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Ambroise
{
    /** @var Color|null */
    private $color;

    public static function idle(): self
    {
        return new self(null);
    }

    public static function at(Color $color): self
    {
        return new self($color);
    }

    private function __construct(?Color $color)
    {
       $this->color = $color;
    }

    public function color(): ?Color
    {
        return $this->color;
    }
}
