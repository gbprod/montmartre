<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Players implements \IteratorAggregate
{
    private $current;
    private $next;
    private $others;
    private $active;

    private function __construct(?Player $current, ?Player $active, ?Player $next, array $others)
    {
        $this->current = $current;
        $this->active = $active;
        $this->next = $next;
        $this->others = $others;
    }

    public static function place(Player ...$players): Players
    {
        return new self(null, null, null, $players);
    }

    public static function from(Player $current, Player $active, Player $next, array $others): Players
    {
        return new self($current, $active, $next, $others);
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
    public function count(): int
    {
        return count($this->all());
    }

    public function toNext(): self
    {
        $nextPosition = ($this->active()->position() + 1) % $this->count();

        return new self(
            $this->next,
            $this->active,
            current(array_filter(
                $this->all(),
                function (Player $player) use ($nextPosition): bool {
                    return $player->position() === $nextPosition;
                })
            ),
            $this->others
        );
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
