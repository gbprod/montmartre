<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Color
{
    private $color;

    public const GREEN = 'green';
    public const BLUE = 'blue';
    public const YELLOW = 'yellow';
    public const PINK = 'pink';

    private function __construct(string $color)
    {
        $this->color = $color;
    }

    public static function fromString(string $color)
    {
        if (!in_array($color, [self::GREEN, self::BLUE, self::PINK, self::YELLOW])) {
            throw new \InvalidArgumentException("Invalid color");
        }

        return new self($color);
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
