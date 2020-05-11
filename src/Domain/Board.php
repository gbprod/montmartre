<?php

namespace GBProd\Montmartre\Domain;

final class Board
{
    private $collectors;
    private $gazettes;
    private $decks;

    private function __construct(Collectors $collectors, Gazettes $gazettes, Decks $decks)
    {
        $this->collectors = $collectors;
        $this->gazettes = $gazettes;
        $this->decks = $decks;
    }

    public static function setup(): self
    {
        return new self(
            Collectors::distribute(),
            Gazettes::distribute(),
            Decks::distribute()
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
            Decks::fromRemaining(...array_map(function ($number) use ($state): Deck {
                return Deck::fromRemaining(
                    ...array_map(function ($museState): Muse {
                        $color = $museState['muse_color'];

                        return Muse::painted(Color::$color(), (int) $museState['muse_value']);
                    }, $state['decks'][$number])
                );
            }, [1, 2, 3]))
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
}
