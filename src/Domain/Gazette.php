<?php

namespace GBProd\Montmartre\Domain;

final class Gazette
{
    private $nbDiff;
    private $value;

    private function __construct(int $nbDiff, int $value)
    {
        $this->nbDiff = $nbDiff;
        $this->value = $value;
    }

    public static function forPublishing(int $nbDiff, int $value)
    {
        return new self($nbDiff, $value);
    }

    public function nbDiff(): int
    {
        return $this->nbDiff;
    }

    public function value(): int
    {
        return $this->value;
    }
}
