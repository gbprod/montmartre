<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Gazettes implements \IteratorAggregate
{
    /** @var Gazette[] */
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

    public static function empty(): self
    {
        return new self();
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->gazettes);
    }

    public function nextFor(int $nbDiff): ?Gazette
    {
        return array_reduce($this->gazettes, static function (?Gazette $selected, Gazette $gazette) use ($nbDiff): ?Gazette {
            return ($gazette->nbDiff() === $nbDiff && (null === $selected || $gazette->value() > $selected->value()))
                ? $gazette
                : $selected;
        }, null);
    }

    public function toArray(): array
    {
        return array_map(function (Gazette $gazette): array {
            return [
                'nbDiff' => $gazette->nbDiff(),
                'value' => $gazette->value(),
            ];
        }, $this->gazettes);
    }

    public function count(): int
    {
        return count($this->gazettes);
    }

    public function buy(Gazette $gazette): void
    {
        $this->gazettes = array_filter(
            $this->gazettes,
            static function (Gazette $availableGazette) use ($gazette): bool {
                return !$gazette->equals($availableGazette);
            }
        );
    }
}
