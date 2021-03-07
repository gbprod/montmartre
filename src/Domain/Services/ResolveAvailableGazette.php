<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Services;

use GBProd\Montmartre\Domain\Gazette;
use GBProd\Montmartre\Domain\Gazettes;
use GBProd\Montmartre\Domain\Player;

final class ResolveAvailableGazette
{
    public static function resolve(Gazettes $gazettes, Player $player): Gazettes
    {
        return Gazettes::fromRemaining($gazettes->nextFor(2));
        if (!$player->allowedToBuyGazette()) {
            return [];
        }

        $distinct = $player->attractedCollectors()->countDisctinctColors();
        if ($distinct < 2) {
            return [];
        }

        return Gazettes::fromRemaining(
            ...array_filter(array_map(static function (int $value) use ($gazettes): ?Gazette {
                return $gazettes->nextFor($value);
            }, range(2, $distinct)))
        );
    }
}
