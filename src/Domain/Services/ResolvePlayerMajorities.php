<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Services;

use GBProd\Montmartre\Domain\Color;
use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Player;
use GBProd\Montmartre\Domain\Players;


final class ResolvePlayerMajorities
{
    /**
     * @return Color[]
     */
    public static function resolve(Players $players): array
    {
        $counting = self::countValues($players->current());
        if (empty($counting)) {
            return [];
        }

        $maxCounting = self::maxCounting($players);

        $majorities = [];
        foreach ($counting as $color => $counts) {
            if (
                $counts['values'] >= ($maxCounting[$color]['values'] ?? 0)
                || $counts['count'] >= ($maxCounting[$color]['count'] ?? 0)
            ) {
                $majorities[] = Color::fromString($color);
            }
        }

        return $majorities;
    }

    private static function maxCounting(Players $players): array
    {
       $maxCounting = [];

        foreach ($players->others() as $otherPlayer) {
            $otherPlayerCounting = self::countValues($otherPlayer);

            foreach ($otherPlayerCounting as $color => $counts) {
                $maxCounting[$color] = [
                    'values' => max($counts['values'], isset($maxCounting[$color]) ? $maxCounting[$color]['values'] : 0),
                    'count' => max($counts['count'], isset($maxCounting[$color]) ? $maxCounting[$color]['count'] : 0),
                ];
            }
        }

        return $maxCounting;
    }

    private static function countValues(Player $player): array
    {
        return array_reduce(
            $player->paintings()->muses(),
            static function(array $carry, Muse $muse): array {
                $carry[$muse->color()->value()] = isset($carry[$muse->color()->value()])
                    ? [
                        'count' => $carry[$muse->color()->value()]['count'] + 1,
                        'values' => $carry[$muse->color()->value()]['values'] + $muse->value(),
                    ]
                    : [
                        'count' => 1,
                        'values' => $muse->value(),
                    ];

                return $carry;
            }, []
        );
    }
}
