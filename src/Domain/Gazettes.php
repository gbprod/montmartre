<?php

namespace GBProd\Montmartre\Domain;

final class Gazettes implements \IteratorAggregate
{
    private $gazettes;

    private function __construct(Gazette ...$gazettes)
    {
        $this->gazettes = $gazettes;
    }

    public static function distribute(): self
    {
        return new self(
            Gazette::forPublishing(2, 4),
            Gazette::forPublishing(3, 7),
            Gazette::forPublishing(3, 3),
            Gazette::forPublishing(4, 12),
            Gazette::forPublishing(4, 5)
        );
    }

    public static function fromRemaining(Gazette ...$gazettes): self
    {
        return new self(...$gazettes);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->gazettes);
    }
}
