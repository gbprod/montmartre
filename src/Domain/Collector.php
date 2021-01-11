<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Collector
{
    /** @var int */
    private $value;
    /** @var Color */
    private $color;

    private function __construct(int $value, Color $color)
    {
        $this->value = $value;
        $this->color = $color;
    }

    public static function blue(int $value): self
    {
        return new self($value, Color::blue());
    }

    public static function green(int $value): self
    {
        return new self($value, Color::green());
    }

    public static function pink(int $value): self
    {
        return new self($value, Color::pink());
    }

    public static function yellow(int $value): self
    {
        return new self($value, Color::yellow());
    }

    public function willPay(): int
    {
        return $this->value;
    }

    public function color(): Color
    {
        return $this->color;
    }
}
