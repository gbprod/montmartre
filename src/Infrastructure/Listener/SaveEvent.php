<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure\Listener;

use GBProd\Montmartre\Domain\Event\Event;

final class SaveEvent
{
    const INSERT_EVENT = <<<SQL
        INSERT INTO events (name, payload)
        VALUES ("%s", "%s");
SQL;

    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function __invoke(Event $event): void
    {
        ($this->table)::dbQuery(
            sprintf(
                self::INSERT_EVENT,
                (new \ReflectionClass($event))->getShortName(),
                ($this->table)->escapeStringForDB(\json_encode($event->toArray()))
            )
        );
    }
}

