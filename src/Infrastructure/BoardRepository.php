<?php

namespace GBProd\Montmartre\Infrastructure;

use GBProd\Montmartre\Domain\Board;

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

    const INSERT_GAZETTES_QUERY = <<<SQL
        INSERT INTO gazettes (board_id, `value`, nb_diff)
        VALUES (%s, %s, %s);
SQL;

    const SELECT_QUERY = <<<SQL
        SELECT * FROM board
        ORDER BY id DESC
        LIMIT 1;
SQL;

    const SELECT_GAZETTES_QUERY = <<<SQL
        SELECT * FROM gazettes
        WHERE board_id = %s ;
SQL;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function save(Board $board): void
    {
        ($this->table)::dbQuery(sprintf(
            self::INSERT_QUERY,
            $board->collectors()->blue()->willPay(),
            $board->collectors()->yellow()->willPay(),
            $board->collectors()->green()->willPay(),
            $board->collectors()->pink()->willPay()
        ));

        $result = ($this->table)::dbQuery(self::SELECT_QUERY);
        $newBoardState = mysqli_fetch_assoc($result);

        /** @var Gazette $gazette */
        foreach ($board->gazettes() as $gazette) {
            ($this->table)::dbQuery(sprintf(
                self::INSERT_GAZETTES_QUERY,
                $newBoardState['id'],
                $gazette->value(),
                $gazette->nbDiff()
            ));
        }
    }

    public function get(): Board
    {
        $result = ($this->table)::dbQuery(self::SELECT_QUERY);
        $state = mysqli_fetch_assoc($result);

        $result = ($this->table)::dbQuery(
            sprintf(self::SELECT_GAZETTES_QUERY, $state['id'])
        );

        $state['gazettes'] = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return Board::fromState($state);
    }
}
