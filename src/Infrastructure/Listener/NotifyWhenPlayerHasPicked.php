<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasPicked;
use GBProd\Montmartre\Domain\Muse;

final class NotifyWhenPlayerHasPicked
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasPicked $event): void
    {
        $this->table->notifyAllPlayers(
            'PlayerHasPicked',
            clienttranslate('${player_name} has picked : ${musesAsString} from deck ${deck_number}'),
            [
                'player_id' => $event->player()->id(),
                'player_name' => $event->player()->name(),
                'deck_number' => $event->deckNumber(),
                'next_muse' => $event->deck()->next()->toArray(),
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
