<?php

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Gazette;
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
            'collectors' => [
                'blue' => $board->collectors()->blue()->willPay(),
                'green' => $board->collectors()->green()->willPay(),
                'yellow' => $board->collectors()->yellow()->willPay(),
                'pink' => $board->collectors()->pink()->willPay(),
            ],
            'gazettes' => array_map(
                static function (Gazette $gazette) {
                    return [
                        'nbDiff' => $gazette->nbDiff(),
                        'value' => $gazette->value(),
                    ];
                },
                iterator_to_array($board->gazettes())
            ),
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
            ]
        ];
    }
}
