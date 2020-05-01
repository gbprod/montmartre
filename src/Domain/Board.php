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

    public function collectors()
    {
        return $this->collectors;
    }
}
