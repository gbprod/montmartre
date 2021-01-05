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
        $this->table->log(__CLASS__);
        $this->table->gamestate->nextState('nextPlayer');
    }
}
