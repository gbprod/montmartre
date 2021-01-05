<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Collectors
{
    /** @var Collector */
    private $blue;
    /** @var Collector */
    private $yellow;
    /** @var Collector */
    private $green;
    /** @var Collector */
    private $pink;

    private function __construct(
        Collector $blue,
        Collector $yellow,
        Collector $green,
        Collector $pink
    ) {
        $this->blue = $blue;
        $this->yellow = $yellow;
        $this->green = $green;
        $this->pink = $pink;
    }

    public static function distribute(): Collectors
    {
        return new self(
            Collector::paying(2),
            Collector::paying(2),
            Collector::paying(2),
            Collector::paying(2)
        );
    }

    public static function paying(
        int $bluePay,
        int $yellowPay,
        int $greenPay,
        int $pinkPay
    ): Collectors {
        return new self(
            Collector::paying($bluePay),
            Collector::paying($yellowPay),
            Collector::paying($greenPay),
            Collector::paying($pinkPay)
        );
    }

    public function blue(): ?Collector
    {
        return $this->blue;
    }

    public function yellow(): ?Collector
    {
        return $this->yellow;
    }

    public function green(): ?Collector
    {
        return $this->green;
    }

    public function pink(): ?Collector
    {
        return $this->pink;
    }

    public function draw(Color $color): ?Collector
    {
        $collector = $this->{$color->value()}();

        if (null === $collector) {
            throw new \InvalidArgumentException();
        }

        $this->{$color->value()} = $collector->willPay() <= 10
            ? Collector::paying($collector->willPay() + 2)
            : null;

        return $collector;
    }

    public function toArray(): array
    {
        return [
            'blue' => null !== $this->blue() ? $this->blue()->willPay() : null,
            'green' => null !== $this->green() ? $this->green()->willPay() : null,
            'yellow' => null !== $this->yellow() ? $this->yellow()->willPay() : null,
            'pink' => null !== $this->pink() ? $this->pink()->willPay() : null,
        ];
    }
}
