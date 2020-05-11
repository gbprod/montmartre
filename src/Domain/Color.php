<?php

namespace GBProd\Montmartre\Domain;

final class Color
{
    private const GREEN = 'green';
    private const BLUE = 'blue';
    private const YELLOW = 'yellow';
    private const PINK = 'pink';

    private function __construct(string $color)
    {
        $this->color = $color;
    }

    public static function green(): self
    {
        return new self(self::GREEN);
    }

    public static function blue(): self
    {
        return new self(self::BLUE);
    }

    public static function yellow(): self
    {
        return new self(self::YELLOW);
    }

    public static function pink(): self
    {
        return new self(self::PINK);
    }

    public function value(): string
    {
        return $this->color;
    }

    public function __toString(): string
    {
        return $this->color;
    }
}
