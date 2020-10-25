<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasPaint;

final class UpdateGameStateOnPlayerHasPaint
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasPaint $event): void
    {
        if (count($event->player()->hand()->muses()) > 6) {
            $this->table->gamestate->nextState('mustSellOffState');
        }

        $this->table->gamestate->nextState('sellOffState');
    }
}
