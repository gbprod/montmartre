<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasChanged;

final class UpdateGameStateOnPlayerHasChanged
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasChanged $event): void
    {
        $this->table->giveExtraTime($event->player()->id());
        $this->table->gamestate->changeActivePlayer($event->player()->id());
        $this->table->gamestate->nextState('playerTurn');
    }
}
