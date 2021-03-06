<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

use GBProd\Montmartre\Domain\Muse;

final class MusesHasBeenDiscarded implements Event
{
    /** @var array */
    private $muses;

    public function __construct(Muse ...$muses)
    {
        $this->muses = $muses;
    }

    public function muses(): array
    {
        return $this->muses;
    }

    public function toArray(): array
    {
        return \array_map(function(Muse $muse): array {
            return $muse->toArray();
        }, $this->muses());
    }
}
