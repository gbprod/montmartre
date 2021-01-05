<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasDrawed;
use GBProd\Montmartre\Domain\Muse;

final class NotifyWhenPlayerHasDrawed
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasDrawed $event): void
    {
        $this->table->notifyAllPlayers(
            'PlayerHasDrawed',
            clienttranslate('${player_name} has drawed : ${musesAsString} from deck ${deck_number}'),
            [
                'player_id' => $event->player()->id(),
                'player_name' => $event->player()->name(),
                'deck_number' => $event->deckNumber(),
                'next_muse' => null !== $event->deck()->next() ? $event->deck()->next()->toArray() : null,
                'musesAsString' => implode(', ', array_map(function (Muse $muse): string {
                    return sprintf(
                        '%s %s',
                        $muse->value(),
                        clienttranslate($muse->color()->value())
                    );
                }, $event->muses())),
                'muses' => array_map(function (Muse $muse): array {
                    return $muse->toArray();
                }, $event->muses()),
            ]
        );
    }
}
