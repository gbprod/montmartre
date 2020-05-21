<?php

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Muse;

final class PaintAction
{
    public $muses;

    public static function fromMuses(Muse ...$muses): self
    {
        $self = new self();
        $self->muses = $muses;

        return $self;
    }
}
