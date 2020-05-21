<?php

namespace GBProd\Montmartre\Domain\Event;

final class Events implements \IteratorAggregate, \Countable
{
    private $events;

    public function __construct(Event ...$events)
    {
        $this->events = $events;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->events);
    }

    public function count(): int
    {
        return \count($this->events);
    }
}
