<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

final class DiscardPile implements IteratorAggregate
{
    /** @var Muse[] */
    private $muses;

    private function __construct(Muse ...$muses)
    {
        $this->muses = $muses;
    }

    public static function empty(): self
    {
       return new self();
    }

    public static function from(Muse ...$muses): self
    {
        return new self(...$muses);
    }

    public function flush(): void
    {
        $this->muses = [];
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->muses);
    }
}
