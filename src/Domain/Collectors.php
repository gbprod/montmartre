<?php

namespace GBProd\Montmartre\Domain;

final class Collectors
{
    private $blue;
    private $yellow;
    private $green;
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

    public static function paying(int $bluePay, int $yellowPay, int $greenPay, int $pinkPay): Collectors
    {
        return new self(
            Collector::paying($bluePay),
            Collector::paying($yellowPay),
            Collector::paying($greenPay),
            Collector::paying($pinkPay)
        );
    }

    public function blue(): Collector
    {
        return $this->blue;
    }

    public function yellow(): Collector
    {
        return $this->yellow;
    }

    public function green(): Collector
    {
        return $this->green;
    }

    public function pink(): Collector
    {
        return $this->pink;
    }

    public function toArray(): array
    {
        return [
            'blue' => $this->blue()->willPay(),
            'green' => $this->green()->willPay(),
            'yellow' => $this->yellow()->willPay(),
            'pink' => $this->pink()->willPay(),
        ];
    }
}
