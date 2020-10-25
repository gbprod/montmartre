<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Muse;

final class SellOffAction
{
    public $muses;

    public static function fromMuses(Muse ...$muses): self
    {
        $self = new self();
        $self->muses = $muses;

        return $self;
    }
}
