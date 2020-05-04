<?php

namespace GBProd\Montmartre\Domain;

final class Board
{
    private $collectors;

    private function __construct(Collectors $collectors)
    {
        $this->collectors = $collectors;
    }

    public static function initialize()
    {
        return new self(
            Collectors::initialize()
            // ,
            // Gazettes,
            // Muses,
            // Ambroise,
            // Pieces
        );
    }

    public static function fromState(array $state)
    {
        return new self(
            Collectors::paying(
                (int) $state['collector_blue'],
                (int) $state['collector_yellow'],
                (int) $state['collector_green'],
                (int) $state['collector_pink']
            )
        );
    }

    public function collectors()
    {
        return $this->collectors;
    }
}
