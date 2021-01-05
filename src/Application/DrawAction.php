<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Muse;

final class DrawAction
{
    /** @var int */
    public $deck;

    public static function fromDeckId(int $deck): self
    {
        if ($deck < 1 || $deck > 3) {
           throw new \InvalidArgumentException("Error Processing Request");
        }

        $self = new self();
        $self->deck = $deck;

        return $self;
    }
}
