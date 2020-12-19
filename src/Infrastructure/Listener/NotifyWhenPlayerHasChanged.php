<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Color;
use GBProd\Montmartre\Domain\Event\PlayerHasChanged;
use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Services\ResolvePlayerMajorities;
use function array_map;

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
            'PlayerHasChanged',
            clienttranslate('${player_name}\'s turn'),
            [
                'player_id' => $event->players()->current()->id(),
                'player_name' => $event->players()->current()->name(),
                'majorities' => array_map(function (Color $color): string {
                    return $color->value();
                }, ResolvePlayerMajorities::resolve($event->players())),
            ]
        );
    }
}
