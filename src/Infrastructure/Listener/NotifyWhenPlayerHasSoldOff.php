<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasSoldOff;
use GBProd\Montmartre\Domain\Muse;

final class NotifyWhenPlayerHasSoldOff
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasSoldOff $event): void
    {
        $this->table->notifyAllPlayers(
            'PlayerHasSoldOff',
            clienttranslate('${player_name} has sold off : ${musesAsString}'),
            [
                'player_id' => $event->player()->id(),
                'player_name' => $event->player()->name(),
                'player_score' => $event->player()->wallet()->amount(),
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
