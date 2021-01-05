<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasSold;

final class UpdateGameStateOnPlayerHasSold
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasSold $event): void
    {
        $this->table->log(__CLASS__);
        $this->table->gamestate->nextState('nextPlayer');
    }
}
