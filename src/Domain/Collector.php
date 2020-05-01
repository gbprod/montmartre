<?php

namespace GBProd\Montmartre\Domain;

final class Collector
{
    private $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function paying(int $value)
    {
        return new self($value);
    }

    public function willPay()
    {
        return $this->value;
    }
}
