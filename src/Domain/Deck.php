<?php

namespace GBProd\Montmartre\Domain;

final class Deck implements \Countable
{
    private $muses;

    private function __construct(Muse ...$muses)
    {
        $this->muses = $muses;
    }

    public static function fromRemaining(Muse ...$muses): self
    {
        return new self(...$muses);
    }

    public function muses(): array
    {
        return $this->muses;
    }

    public function next(): ?Muse
    {
        return count($this->muses()) > 0
            ? $this->muses()[0]
            : null;
    }

    public function count(): int
    {
        return count($this->muses());
    }
}
