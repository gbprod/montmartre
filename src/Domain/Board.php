<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

use GBProd\Montmartre\Domain\Event\BoardHasBeenSetUp;
use GBProd\Montmartre\Domain\Event\EventRecordingCapabilities;
use GBProd\Montmartre\Domain\Event\PlayerHasPaint;

final class Board
{
    use EventRecordingCapabilities;

    private $collectors;
    private $gazettes;
    private $decks;
    private $players;

    private function __construct(
        Collectors $collectors,
        Gazettes $gazettes,
        Decks $decks,
        Players $players
    ) {
        $this->collectors = $collectors;
        $this->gazettes = $gazettes;
        $this->decks = $decks;
        $this->players = $players;
    }

    public static function setup(array $players): self
    {
        $initialDeck = Deck::full();

        $players = Players::place(
            ...array_map(
                function ($playerState, $playerId) use ($initialDeck) {
                    return Player::named(
                        $playerId,
                        $playerState['player_name'],
                        Hand::containing(...$initialDeck->draw(5)),
                        Paintings::empty()
                    );
                },
                $players,
                array_keys($players)
            )
        );

        $self = new self(
            Collectors::distribute(),
            Gazettes::distribute(),
            Decks::distribute($initialDeck),
            $players
        );


        $self->recordThat(new BoardHasBeenSetUp());

        return $self;
    }

    public static function fromState(array $state): self
    {
        return new self(
            Collectors::paying(
                (int) $state['collector_blue'],
                (int) $state['collector_yellow'],
                (int) $state['collector_green'],
                (int) $state['collector_pink']
            ),
            Gazettes::fromRemaining(
                ...array_map(static function ($gazetteState) {
                    return Gazette::forPublishing(
                        (int) $gazetteState['nb_diff'],
                        (int) $gazetteState['value']
                    );
                }, $state['gazettes'])
            ),
            Decks::fromRemaining(
                ...array_map(function ($number) use ($state): Deck {
                    return Deck::fromRemaining(
                        ...array_map(function ($museState): Muse {
                            $color = $museState['muse_color'];

                            return Muse::painted(
                                Color::$color(),
                                (int) $museState['muse_value']
                            );
                        }, $state['decks'][$number])
                    );
                }, [1, 2, 3])
            ),
            Players::from(
                ...array_reduce(
                    $state['players'],
                    function ($carry, $item) use ($state) {
                        if ($item['player_id'] == $state['current_player']) {
                            $carry[0] = Player::fromState($item);
                        }

                        if ($item['player_id'] == $state['current_player']) {
                            $carry[1] = Player::fromState($item);
                        }

                        if (
                            $item['player_id'] != $state['current_player']
                            && $item['player_id'] != $state['current_player']
                        ) {
                            $carry[2][] = Player::fromState($item);
                        }

                        return $carry;
                    },
                    [null, null, []]
                )
            )
        );
    }

    public function collectors(): Collectors
    {
        return $this->collectors;
    }

    public function gazettes(): Gazettes
    {
        return $this->gazettes;
    }

    public function decks(): Decks
    {
        return $this->decks;
    }

    public function players(): Players
    {
        return $this->players;
    }

    public function paint(Muse ...$muses): void
    {
        $this->players()
            ->current()
            ->paint(...$muses);

        $this->recordThat(new PlayerHasPaint(
            $this->players()->current(),
            ...$muses
        ));
    }
}
