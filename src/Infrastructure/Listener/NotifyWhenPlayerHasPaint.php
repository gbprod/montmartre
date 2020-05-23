<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\PlayerHasPaint;

final class NotifyWhenPlayerHasPaint
{
    public function __invoke(PlayerHasPaint $event): void
    {
    }
}
