<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Player;

final class PlayerHasSoldOff implements Event
{
    private $player;
    private $muses;

    public function __construct(
        Player $player,
        Muse ...$muses
    ) {
        $this->player = $player;
        $this->muses = $muses;
    }

    public function muses()
    {
        return $this->muses;
    }

    public function player(): Player
    {
        return $this->player;
    }
}
