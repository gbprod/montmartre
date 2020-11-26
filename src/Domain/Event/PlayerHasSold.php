<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Collector;
use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Player;

final class PlayerHasSold implements Event
{
    /** @var Player */
    private $player;
    /** @var Muse */
    private $muse;
    /** @var Collector */
    private $attractedCollector;
    /** @var Collector|null */
    private $newCollector;

    public function __construct(
        Player $player,
        Muse $muse,
        Collector $attractedCollector,
        ?Collector $newCollector
    ) {
        $this->player = $player;
        $this->muse = $muse;
        $this->attractedCollector = $attractedCollector;
        $this->newCollector = $newCollector;
    }

    public function muse(): Muse
    {
        return $this->muse;
    }

    public function player(): Player
    {
        return $this->player;
    }

    public function attractedCollector(): Collector
    {
        return $this->attractedCollector;
    }

    public function newCollector(): ?Collector
    {
        return $this->newCollector;
    }
}
