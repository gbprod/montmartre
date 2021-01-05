<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasSoldOff;

final class UpdateGameStateOnPlayerHasSoldOff
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasSoldOff $event): void
    {
        $this->table->log(__CLASS__);
        $this->table->gamestate->nextState('drawState');
    }
}
