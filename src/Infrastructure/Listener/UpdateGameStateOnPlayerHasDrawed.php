<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasDrawed;

final class UpdateGameStateOnPlayerHasDrawed
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasDrawed $event): void
    {
        $this->table->gamestate->nextState(
            $event->player()->hand()->isFull() ? 'nextPlayer' : 'drawState'
        );
    }
}
