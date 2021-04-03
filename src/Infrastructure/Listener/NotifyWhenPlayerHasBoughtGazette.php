<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasBoughtGazette;
use GBProd\Montmartre\Domain\Muse;

final class NotifyWhenPlayerHasBoughtGazette
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasBoughtGazette $event): void
    {
        $this->table->notifyAllPlayers(
            'PlayerHasBoughtGazette',
            clienttranslate('${player_name} has bought : ${gazetteAsString}'),
            [
                'player_id' => $event->player()->id(),
                'player_name' => $event->player()->name(),
                'player_score' => $event->player()->wallet()->amount(),
                'gazetteAsString' => 'for ' . $event->gazette()->nbDiff() . ' paintings and earn ' . $event->gazette()->value() . 'F',
                'gazette' => $event->gazette()->toArray(),
            ]
        );
    }
}
