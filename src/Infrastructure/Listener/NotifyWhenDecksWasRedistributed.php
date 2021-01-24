<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\DecksWasRedistributed;
use GBProd\Montmartre\Domain\Muse;

final class NotifyWhenDecksWasRedistributed
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(DecksWasRedistributed $event): void
    {
        $this->table->notifyAllPlayers(
            'DecksWasRedistributed',
            clienttranslate('Decks has been redistributed'),
            [
                'decks' => $event->toArray(),
            ]
        );
    }
}
