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
        if (count($event->availableGazettes()) > 0) {
            $this->table->gamestate->nextState('canBuyGazetteState');

            return;
        }

        $this->table->gamestate->nextState('nextPlayer');
    }
}
