<?php

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

    public static function distribute(): self
    {
        $allMuses = array_map(
            static function (string $color) {
                return array_map(
                    static function ($value) use ($color) {
                        return Muse::painted(Color::$color(), $value);
                    },
                    [0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 7, 8]
                );
            },
            ['blue', 'green', 'pink', 'yellow']
        );

        shuffle($allMuses);

        $deckSize = count($allMuses) / 3;

        return new self(
            Deck::fromRemaining(
                ...array_slice($allMuses, 0, $deckSize)
            ),
            Deck::fromRemaining(
                ...array_slice($allMuses, $deckSize, $deckSize)
            ),
            Deck::fromRemaining(
                ...array_slice($allMuses, $deckSize * 2, $deckSize)
            )
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
}
