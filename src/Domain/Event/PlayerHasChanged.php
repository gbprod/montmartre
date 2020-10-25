<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Player;

final class PlayerHasChanged implements Event
{
    /** @var Player */
    private $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function player(): Player
    {
        return $this->player;
    }
}
