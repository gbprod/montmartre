<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasSold;
use GBProd\Montmartre\Domain\Gazette;
use GBProd\Montmartre\Domain\Muse;

final class NotifyWhenPlayerHasSold
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(PlayerHasSold $event): void
    {
        $this->table->notifyAllPlayers(
            'PlayerHasSold',
            clienttranslate('${player_name} has sold to ${color} collector: ${museAsString}'),
            [
                'player_id' => $event->player()->id(),
                'player_name' => $event->player()->name(),
                'player_score' => $event->player()->wallet()->amount(),
                'museAsString' => sprintf('%s %s',
                    $event->muse()->value(),
                    clienttranslate($event->muse()->color()->value())
                ),
                'muse' => $event->muse()->toArray(),
                'color' => $event->attractedCollector()->color()->value(),
                'attractedCollector' => $event->attractedCollector()->willPay(),
                'newCollector' => null !== $event->newCollector() ? $event->newCollector()->willPay() : null,
                'availableGazettes' => array_map(function (Gazette $gazette): array {
                    return $gazette->toArray();
                }, $event->availableGazettes()),
            ]
        );
    }
}
