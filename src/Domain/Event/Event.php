<?php

namespace GBProd\Montmartre\Domain\Event;

interface Event
{
    public function payload(): array;
}
