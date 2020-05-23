<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Decks
{
    private $firstDeck;
    private $secondDeck;
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
}
