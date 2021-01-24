<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Decks
{
    /** @var Deck */
    private $firstDeck;
    /** @var Deck */
    private $secondDeck;
    /** @var Deck */
    private $thirdDeck;

    private function __construct(Deck $firstDeck, Deck $secondDeck, Deck $thirdDeck)
    {
        $this->firstDeck = $firstDeck;
        $this->secondDeck = $secondDeck;
        $this->thirdDeck = $thirdDeck;
    }

    public static function distribute(Deck $fromDeck): self
    {
        return new self(
            $fromDeck->sliceToThird(),
            $fromDeck->sliceToHalf(),
            $fromDeck
        );
    }

    public static function fromRemaining(Deck $firstDeck, Deck $secondDeck, Deck $thirdDeck): self
    {
        return new self($firstDeck, $secondDeck, $thirdDeck);
    }

    public function firstDeck(): Deck
    {
        return $this->firstDeck;
    }

    public function secondDeck(): Deck
    {
        return $this->secondDeck;
    }

    public function thirdDeck(): Deck
    {
        return $this->thirdDeck;
    }

    public function toArray(): array
    {
        return array_map(
            function (Deck $deck): array {
                return array_map(function (Muse $muse): array {
                    return $muse->toArray();
                }, $deck->muses());
            },
            [$this->firstDeck, $this->secondDeck, $this->thirdDeck]
        );
    }

    public function byNumber(int $number): Deck
    {
        switch ($number) {
            case 1:
                return $this->firstDeck();
            case 2:
                return $this->secondDeck();
            case 3:
                return $this->thirdDeck();
        }

        throw new \InvalidArgumentException('Invalid deck number');
    }

    public function mustBeRedistibuted(): bool
    {
        return array_sum([
            $this->firstDeck()->isEmpty(),
            $this->secondDeck()->isEmpty(),
            $this->thirdDeck()->isEmpty()
        ]) >= 2;
    }

    public function remainingMuses(): array
    {
        return array_merge(
            $this->firstDeck()->muses(),
            $this->secondDeck()->muses(),
            $this->thirdDeck()->muses()
        );
    }
}
