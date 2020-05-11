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

    const INSERT_MUSE_QUERY = <<<SQL
        INSERT INTO deck_cards (board_id, deck_number, position, muse_value, muse_color)
        VALUES (%s, %s, %s, %s, "%s");
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

    const SELECT_DECKS_QUERY = <<<SQL
        SELECT * FROM deck_cards
        WHERE board_id = %s AND deck_number = %s
        ORDER BY position ASC;
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

        $this->storeMuses($newBoardState['id'], 1, $board->decks()->firstDeck()->muses());
        $this->storeMuses($newBoardState['id'], 2, $board->decks()->secondDeck()->muses());
        $this->storeMuses($newBoardState['id'], 3, $board->decks()->thirdDeck()->muses());
    }

    private function storeMuses(int $boardId, int $deckNumber, array $muses): void
    {
        for ($position = 0; $position < count($muses); $position++) {
            ($this->table)::dbQuery(sprintf(
                self::INSERT_MUSE_QUERY,
                $boardId,
                $deckNumber,
                $position,
                $muses[$position]->value(),
                $muses[$position]->color()->value()
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

        foreach ([1, 2, 3] as $deckNumber) {
            $result = ($this->table)::dbQuery(
                sprintf(self::SELECT_DECKS_QUERY, $state['id'], $deckNumber)
            );

            $state['decks'][$deckNumber] = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        return Board::fromState($state);
    }
}
