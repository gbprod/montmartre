<?php

namespace GBProd\Montmartre\Domain;

final class Board
{
    private $collectors;
    private $gazettes;
    private $decks;

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
                        Hand::containing(...$initialDeck->draw(5))
                    );
                },
                $players,
                array_keys($players)
            )
        );

        return new self(
            Collectors::distribute(),
            Gazettes::distribute(),
            Decks::distribute($initialDeck),
            $players
            // Ambroise,
            // Pieces
        );
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
                        $gazetteState['nb_diff'],
                        $gazetteState['value']
                    );
                }, $state['gazettes'])
            ),
            Decks::fromRemaining(
                ...array_map(function ($number) use ($state): Deck {
                    return Deck::fromRemaining(
                        ...array_map(function ($museState): Muse {
                            $color = $museState['muse_color'];

                            return Muse::painted(Color::$color(), (int) $museState['muse_value']);
                        }, $state['decks'][$number])
                    );
                }, [1, 2, 3])
            ),
            Players::from(
                ...array_reduce(
                    $state['players'],
                    function ($carry, $item) use ($state) {
                        if ($item['player_id'] == $state['current_player']) {
                            $carry[0] = Player::named(
                                $item['player_id'],
                                $item['player_name'],
                                Hand::containing(
                                    ...array_map(
                                        function ($handCard) {
                                            $color = $handCard['muse_color'];
                                            return Muse::painted(Color::$color(), (int) $handCard['muse_value']);
                                        },
                                        array_filter($state['hands'], function ($handCard) use ($item) {
                                            return $handCard['player_id'] == $item['player_id'];
                                        })
                                    )
                                )
                            );
                        }

                        if ($item['player_id'] == $state['current_player']) {
                            $carry[1] = Player::named(
                                $item['player_id'],
                                $item['player_name'],
                                Hand::containing(
                                    ...array_map(
                                        function ($handCard) {
                                            $color = $handCard['muse_color'];
                                            return Muse::painted(Color::$color(), (int) $handCard['muse_value']);
                                        },
                                        array_filter($state['hands'], function ($handCard) use ($item) {
                                            return $handCard['player_id'] == $item['player_id'];
                                        })
                                    )
                                )
                            );
                        }

                        if ($item['player_id'] != $state['current_player'] && $item['player_id'] != $state['current_player']) {
                            $carry[2][] = Player::named(
                                $item['player_id'],
                                $item['player_name'],
                                Hand::containing(
                                    ...array_map(
                                        function ($handCard) {
                                            $color = $handCard['muse_color'];
                                            return Muse::painted(Color::$color(), (int) $handCard['muse_value']);
                                        },
                                        array_filter($state['hands'], function ($handCard) use ($item) {
                                            return $handCard['player_id'] == $item['player_id'];
                                        })
                                    )
                                )
                            );
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
}
