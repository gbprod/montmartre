<?php

namespace GBProd\Montmartre\Infrastructure;

use GBProd\Montmartre\Domain\Board;


final class BoardRepository
{
    const INSERT_QUERY = <<<SQL
        INSERT OR REPLACE INTO board (
            id,
            collector_blue,
            collector_yellow,
            collector_green,
            collector_pink
        )
        VALUES (1, %s, %s, %s, %s)
        WHERE id=1
    ;
SQL;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function save(Board $board)
    {
        ($this->table)::dbQuery(sprintf(
            self::INSERT_QUERY,
            $board->collectors()->blue()->willPay(),
            $board->collectors()->yellow()->willPay(),
            $board->collectors()->green()->willPay(),
            $board->collectors()->pink()->willPay()
        ));
    }

    public function get()
    {
    }
}
