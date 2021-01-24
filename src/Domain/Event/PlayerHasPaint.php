<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Player;

final class PlayerHasPaint implements Event
{
    /** @var Player */
    private $player;
    /** @var Muse[] */
    private $muses;

    public function __construct(
        Player $player,
        Muse ...$muses
    ) {
        $this->player = $player;
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

    public function toArray(): array
    {
        return [
            'player_id' => $this->player()->id(),
            'muses' => \array_map(function (Muse $muse): array {
                return $muse->toArray();
            }, $this->muses()),
        ];
    }
}
