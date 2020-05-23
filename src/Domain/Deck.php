<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Deck implements \Countable
{
    private $muses;

    private function __construct(Muse ...$muses)
    {
        $this->muses = $muses;
    }

    public static function full(): self
    {
        $muses = array_merge(...array_map(
            static function (string $color) {
                return array_map(
                    static function ($value) use ($color) {
                        return Muse::painted(Color::$color(), $value);
                    },
                    [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 7, 8]
                );
            },
            ['blue', 'green', 'pink', 'yellow']
        ));

        shuffle($muses);

        return new self(...$muses);
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

    public function pick(): Muse
    {
        if (empty($this->muses)) {
            throw new EmptyDeck();
        }

        return array_shift($this->muses);
    }

    public function sliceToThird(): Deck
    {
        if (empty($this->muses)) {
            throw new EmptyDeck();
        }

        $sliceLength = (int) (count($this->muses) / 3);

        $sliceDeck = Deck::fromRemaining(
            ...array_slice($this->muses, 0, $sliceLength)
        );

        $this->muses = array_slice($this->muses, $sliceLength);

        return $sliceDeck;
    }

    public function sliceToHalf(): Deck
    {
        if (empty($this->muses)) {
            throw new EmptyDeck();
        }

        $sliceLength = (int) (count($this->muses) / 2);

        $sliceDeck = Deck::fromRemaining(
            ...array_slice($this->muses, 0, $sliceLength)
        );

        $this->muses = array_slice($this->muses, $sliceLength);

        return $sliceDeck;
    }


    public function draw(int $quatity): array
    {
        if (empty($this->muses)) {
            throw new EmptyDeck();
        }

        $drawed = array_slice($this->muses, 0, $quatity);

        $this->muses = array_slice($this->muses, $quatity);

        return $drawed;
    }
}
