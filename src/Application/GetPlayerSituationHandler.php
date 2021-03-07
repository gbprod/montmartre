<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Color;
use GBProd\Montmartre\Domain\Gazette;
use GBProd\Montmartre\Domain\Muse;
use GBProd\Montmartre\Domain\Player;
use GBProd\Montmartre\Domain\Services\ResolveAvailableGazette;
use GBProd\Montmartre\Domain\Services\ResolvePlayerMajorities;
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
            'ambroise' => null !== $board->ambroise()->color() ? $board->ambroise()->color()->value() : null,
            'decks' => [
                1 => null !== $board->decks()->firstDeck()->next() ? [
                    'color' => $board->decks()->firstDeck()->next()->color()->value(),
                    'value' => $board->decks()->firstDeck()->next()->value(),
                ] : null,
                2 => null !== $board->decks()->secondDeck()->next() ? [
                    'color' => $board->decks()->secondDeck()->next()->color()->value(),
                    'value' => $board->decks()->secondDeck()->next()->value(),
                ] : null,
                3 => null !== $board->decks()->thirdDeck()->next() ? [
                    'color' => $board->decks()->thirdDeck()->next()->color()->value(),
                    'value' => $board->decks()->thirdDeck()->next()->value(),
                ] : null,
            ],
            'current_player' => [
                'id' => $board->players()->current()->id(),
                'hand' => array_map(function (Muse $muse) {
                    return $muse->toArray();
                }, $board->players()->current()->hand()->muses()),
                'majorities' => array_map(static function (Color $color): string {
                    return $color->value();
                }, ResolvePlayerMajorities::resolve($board->players())),
                'gazette' => null !== $board->players()->current()->gazette() ? $board->players()->current()->gazette()->toArray() : null,
                'allowedToBuyGazette' => $board->players()->current()->allowedToBuyGazette(),
                'availableGazettes' => array_map(
                    function (Gazette $gazette): array {
                        return $gazette->toArray();
                    }, iterator_to_array(ResolveAvailableGazette::resolve(
                        $board->gazettes(),
                        $board->players()->current()
                    ))
                ),
            ],
            'players' => array_map(
                function (Player $player) {
                    return [
                        'id' => $player->id(),
                        'name' => $player->name(),
                        'paintings' => array_map(function (Muse $muse): array {
                            return $muse->toArray();
                        }, $player->paintings()->muses()),
                        'wallet' => $player->wallet()->amount(),
                    ];
                },
                $board->players()->all()
            ),
        ];
    }
}
