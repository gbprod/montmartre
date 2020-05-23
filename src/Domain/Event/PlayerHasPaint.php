<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Muse;

final class PlayerHasPaint implements Event
{
    public $playerId;
    public $muses;

    public function __construct(
        int $playerId,
        Muse ...$muses
    ) {
        $this->playerId = $playerId;
        $this->muses = $muses;
    }
}
