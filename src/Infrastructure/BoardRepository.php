<?php

namespace GBProd\Montmartre\Infrastructure;

use GBProd\Montmartre\Domain\Board;
use mysqli;

final class BoardRepository
{
    const INSERT_QUERY = <<<SQL
        INSERT INTO board (
            collector_blue,
            collector_yellow,
            collector_green,
            collector_pink
        )
        VALUES (%s, %s, %s, %s);
SQL;

    const SELECT_QUERY = <<<SQL
        SELECT * FROM board
        ORDER BY id DESC
        LIMIT 1;
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

    /**
     * @return Board
     */
    public function get()
    {
        $result = ($this->table)::dbQuery(self::SELECT_QUERY);
        $state = mysqli_fetch_assoc($result);

        return Board::fromState($state);
    }
}
