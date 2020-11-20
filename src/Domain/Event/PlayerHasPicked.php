<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Deck;
use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Player;

final class PlayerHasPicked implements Event
{
    private $player;
    private $muses;
    private $deck;
    private $deckNumber;

    public function __construct(
        Player $player,
        Deck $deck,
        int $deckNumber,
        Muse ...$muses
    ) {
        $this->player = $player;
        $this->deck= $deck;
        $this->deckNumber = $deckNumber;
        $this->muses = $muses;
    }

    /**
     * @return Muse[]
     */
    public function muses(): array
    {
        return $this->muses;
    }

    public function player(): Player
    {
        return $this->player;
    }

    public function deckNumber(): int
    {
        return $this->deckNumber;
    }

    public function deck(): Deck
    {
        return $this->deck;
    }
}
