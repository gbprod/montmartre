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

    public static function initialize()
    {
        return new self(
            Collector::paying(2),
            Collector::paying(2),
            Collector::paying(2),
            Collector::paying(2)
        );
    }

    public static function paying(int $bluePay, int $yellowPay, int $greenPay, int $pinkPay)
    {
        return new self(
            Collector::paying($bluePay),
            Collector::paying($yellowPay),
            Collector::paying($greenPay),
            Collector::paying($pinkPay)
        );
    }

    public function blue()
    {
        return $this->blue;
    }

    public function yellow()
    {
        return $this->yellow;
    }

    public function green()
    {
        return $this->green;
    }

    public function pink()
    {
        return $this->pink;
    }
}
