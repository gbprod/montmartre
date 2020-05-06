<?php

namespace GBProd\Montmartre\Domain;

final class Board
{
    private $collectors;
    private $gazettes;

    private function __construct(Collectors $collectors, Gazettes $gazettes)
    {
        $this->collectors = $collectors;
        $this->gazettes = $gazettes;
    }

    public static function initialize(): self
    {
        return new self(
            Collectors::initialize(),
            Gazettes::initialize()
            // Muses,
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
}
