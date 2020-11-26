<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure;

use GBProd\Montmartre\Domain\Event\Events;

final class EventDispatcher
{
    /** @var array */
    private $listeners;

    public function __construct(array $listeners)
    {
        $this->listeners = $listeners;
    }

    public function dispatch(Events $events): void
    {
        foreach ($events as $event) {
            if (!array_key_exists(get_class($event), $this->listeners)) {
                continue;
            }

            foreach ($this->listeners[get_class($event)] as $listener) {
                $listener($event);
            }
        }
    }
}
