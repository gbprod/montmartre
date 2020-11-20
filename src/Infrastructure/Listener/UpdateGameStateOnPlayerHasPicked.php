<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasPicked;

final class UpdateGameStateOnPlayerHasPicked
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasPicked $event): void
    {
        $this->table->gamestate->nextState('nextPlayer');
    }
}
