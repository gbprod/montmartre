<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Event\Event;
use GBProd\Montmartre\Domain\Gazette;
use GBProd\Montmartre\Domain\Player;

final class PlayerHasBoughtGazette implements Event
{
    /** @var Gazette */
    private $gazette;
    /** @var Player */
    private $player;

    public function __construct(Gazette $gazette, Player $player)
    {
        $this->gazette = $gazette;
        $this->player = $player;
    }

    public function toArray(): array
    {
        return [
            'gazette' => $this->gazette->toArray(),
            'player_id' => $this->player->id(),
        ];
    }

    public function gazette(): Gazette
    {
        return $this->gazette;
    }

    public function player(): Player
    {
        return $this->player;
    }
}
