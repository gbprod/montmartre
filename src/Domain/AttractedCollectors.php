<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

final class AttractedCollectors implements IteratorAggregate
{
    /** @var AttractedCollector[] */
    private $collectors;

    private function __construct(AttractedCollector ...$collectors)
    {
        $this->collectors = $collectors;
    }

    public static function empty(): self
    {
       return new self();
    }

    public static function from(AttractedCollector ...$collectors): self
    {
        return new self(...$collectors);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->collectors);
    }
}
