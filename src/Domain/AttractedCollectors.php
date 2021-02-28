<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

final class AttractedCollectors implements IteratorAggregate
{
    /** @var Collector[] */
    private $collectors;

    private function __construct(Collector ...$collectors)
    {
        $this->collectors = $collectors;
    }

    public static function empty(): self
    {
        return new self();
    }

    public static function from(Collector ...$collectors): self
    {
        return new self(...$collectors);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->collectors);
    }

    public function countDisctinctColors(): int
    {
        return count(array_unique(array_map(
            static function (Collector $collector) {
                return $collector->color()->value();
            },
            $this->collectors
        )));
    }
}
