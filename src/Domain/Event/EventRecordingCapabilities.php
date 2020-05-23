<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain\Event;

trait EventRecordingCapabilities
{
    private $events = [];

    private function recordThat(Event $event): void
    {
        $this->events[] = $event;
    }

    public function releaseEvents(): Events
    {
        $events = new Events(...$this->events);

        $this->events = [];

        return $events;
    }
}
