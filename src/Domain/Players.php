<?php

namespace GBProd\Montmartre\Domain;

final class Players implements \IteratorAggregate
{
    private $current;
    private $others;

    private function __construct(?Player $current, ?Player $active, array $others)
    {
        $this->current = $current;
        $this->active = $active;
        $this->others = $others;
    }

    public static function place(Player ...$players): Players
    {
        return new self(null, null, $players);
    }

    public static function from(Player $current, Player $active, array $others): Players
    {
        return new self($current, $active, $others);
    }

    public function current(): ?Player
    {
        return $this->current;
    }

    public function active(): ?Player
    {
        return $this->active;
    }

    public function others(): array
    {
        return $this->others;
    }

    public function all(): array
    {
        return array_filter(array_merge(
            [$this->current()],
            $this->others()
        ));
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->all());
    }

    public function toArray(): array
    {
        return array_map(function (Player $player): array {
            return $player->toArray();
        }, $this->all());
    }
}
