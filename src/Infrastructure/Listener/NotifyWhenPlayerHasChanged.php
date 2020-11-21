<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasChanged;
use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Services\ResolvePlayerMajorities;

final class NotifyWhenPlayerHasChanged
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasChanged $event): void
    {
        $this->table->notifyAllPlayers(
            $event->players()->current()->id(),
            'PlayerHasChanged',
            clienttranslate('${player_name}\'s turn'),
            [
                'player_id' => $event->players()->current()->id(),
                'player_name' => $event->players()->current()->name(),
                'majorities' => ResolvePlayerMajorities::resolve($event->players()),
            ]
        );
    }
}
