<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Player;
use GBProd\Montmartre\Infrastructure\BoardRepository;

final class GetPlayerSituationHandler
{
    /** @var BoardRepository */
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetPlayerSituationQuery $query): array
    {
        $board = $this->repository->get();

        return [
            'collectors' => $board->collectors()->toArray(),
            'gazettes' => $board->gazettes()->toArray(),
            'decks' => [
                1 => [
                    'color' => $board->decks()->firstDeck()->next()->color()->value(),
                    'value' => $board->decks()->firstDeck()->next()->value(),
                    'count' => $board->decks()->firstDeck()->count(),
                ],
                2 => [
                    'color' => $board->decks()->secondDeck()->next()->color()->value(),
                    'value' => $board->decks()->secondDeck()->next()->value(),
                    'count' => $board->decks()->secondDeck()->count(),
                ],
                3 => [
                    'color' => $board->decks()->thirdDeck()->next()->color()->value(),
                    'value' => $board->decks()->thirdDeck()->next()->value(),
                    'count' => $board->decks()->secondDeck()->count(),
                ],
            ],
            'current_player' => [
                'id' => $board->players()->current()->id(),
                'name' => $board->players()->current()->name(),
                'hand' => array_map(function (Muse $muse) {
                    return $muse->toArray();
                }, $board->players()->current()->hand()->muses()),
                'paintings' => array_map(function (Muse $muse): array {
                    return $muse->toArray();
                }, $board->players()->current()->paintings()->muses()),
            ],
            'others_players' => array_map(
                function (Player $player) {
                    return [
                        'id' => $player->id(),
                        'name' => $player->name(),
                        'paintings' => array_map(function (Muse $muse): array {
                            return $muse->toArray();
                        }, $player->paintings()->muses()),
                    ];
                    },
                $board->players()->active()->id() !== $board->players()->current()->id() ?
                    array_merge(
                        [$board->players()->active()],
                        $board->players()->others()
                    )
                    : $board->players()->others()
            ),
        ];
    }
}
