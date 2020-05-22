<?php

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Board;

class BoardHasBeenSetUp implements Event
{
    private $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
