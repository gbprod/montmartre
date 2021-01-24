<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Player;
use GBProd\Montmartre\Domain\Players;

final class PlayerHasChanged implements Event
{
    /** @var Players */
    private $players;

    public function __construct(Players $players)
    {
        $this->players = $players;
    }

    public function players(): Players
    {
        return $this->players;
    }

    public function toArray(): array
    {
        return [
            'player_id' => $this->players()->current()->id(),
        ];
    }
}
