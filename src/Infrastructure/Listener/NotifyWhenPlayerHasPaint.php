<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasPaint;
use GBProd\Montmartre\Domain\Muse;

final class NotifyWhenPlayerHasPaint
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasPaint $event): void
    {
        $this->table->notifyAllPlayers(
            'PlayerHasPaint',
            clienttranslate('${player_name} has paint : ${musesAsString}'),
            [
                'player_id' => $event->player->id(),
                'player_name' => $event->player->name(),
                'musesAsString' => implode(', ', array_map(function (Muse $muse): string {
                    return sprintf(
                        '%s %s',
                        $muse->value(),
                        clienttranslate($muse->color()->value())
                    );
                }, $event->muses)),
                'muses' => array_map(function (Muse $muse): array {
                    return $muse->toArray();
                }, $event->muses),
            ]
        );
    }
}
