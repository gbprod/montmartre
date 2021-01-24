<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Decks;

final class DecksWasRedistributed implements Event
{
    /** @var Decks */
    private $decks;

    public function __construct(Decks $decks)
    {
        $this->decks = $decks;
    }

    public function decks(): Decks
    {
        return $this->decks;
    }

    public function toArray(): array
    {
        return [
            1 => null !== $this->decks()->firstDeck()->next() ? $this->decks()->firstDeck()->next()->toArray() : null,
            2 => null !== $this->decks()->secondDeck()->next() ? $this->decks()->secondDeck()->next()->toArray() : null,
            3 => null !== $this->decks()->thirdDeck()->next() ? $this->decks()->thirdDeck()->next()->toArray() : null,
        ];
    }
}
