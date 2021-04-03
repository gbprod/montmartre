<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasBoughtGazette;

final class UpdateGameStateOnPlayerHasBoughtGazette
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasBoughtGazette $event): void
    {
        $this->table->gamestate->nextState('nextPlayer');
    }
}
