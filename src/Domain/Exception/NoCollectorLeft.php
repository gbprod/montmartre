<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Exception;

use GBProd\Montmartre\Domain\Color;

class NoCollectorLeft extends \DomainException
{
    /** @var Color */
    private $color;

    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    public function color(): Color
    {
        return $this->color;
    }
}
