<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Muse;

final class BuyGazetteAction
{
    /** @var int */
    public $nbDiff;

    public static function fromNbDiff(int $nbDiff): self
    {
        if ($nbDiff < 2 || $nbDiff > 4) {
           throw new \InvalidArgumentException('Error Processing Request');
        }

        $self = new self();
        $self->nbDiff = $nbDiff;

        return $self;
    }
}

