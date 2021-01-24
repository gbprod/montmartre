<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

interface Event
{
    public function toArray(): array;
}
