<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Muse;

final class SellAction
{
    /** @var string */
    public $color;

    public static function fromColor(string $color): self
    {
        $self = new self();
        $self->color = $color;

        return $self;
    }
}
