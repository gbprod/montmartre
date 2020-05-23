<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure;

use GBProd\Montmartre\Domain\Event\Event;

final class EventDispatcher
{
    private $listeners;

    public function __construct(array $listeners)
    {
        $this->listeners = $listeners;
    }

    public function dispatch(Event $event): void
    {
        if (!array_key_exists(get_class($event), $this->listeners)) {
            return;
        }

        foreach ($this->listeners[get_class($event)] as $listener) {
            $listener($event);
        }
    }
}
