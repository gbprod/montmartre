<?php

namespace GBProd\Montmartre\Domain;

final class Hand
{
    private $muses;

    private function __construct(Muse ...$muses)
    {
        if (count($muses) > 5) {
            throw new \InvalidArgumentException("Players hand can't contains more than 5 cards");
        }

        $this->muses = $muses;
    }

    public static function containing(Muse ...$muses)
    {
        return new self(...$muses);
    }

    public function muses(): array
    {
        return $this->muses;
    }
}
