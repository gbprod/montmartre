<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Player;

final class PlayerHasPaint implements Event
{
    public $player;
    public $muses;

    public function __construct(
        Player $player,
        Muse ...$muses
    ) {
        $this->player = $player;
        $this->muses = $muses;
    }
}
