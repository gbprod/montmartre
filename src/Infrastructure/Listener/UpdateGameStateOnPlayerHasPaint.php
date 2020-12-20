<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasPaint;
use GBProd\Montmartre\Domain\Paintings;
use function var_dump;

final class UpdateGameStateOnPlayerHasPaint
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasPaint $event): void
    {
        if ($event->player()->paintings()->count() > Paintings::MAX_PAINTINGS) {
            $this->table->gamestate->nextState('mustSellOffState');

            return;
        }

        $this->table->gamestate->nextState('sellOffState');
    }
}
