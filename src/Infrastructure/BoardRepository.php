<?php

namespace GBProd\Montmartre\Infrastructure;

use GBProd\Montmartre\Domain\Board;

final class BoardRepository
{

    const SELECT_QUERY = <<<SQL
        SELECT * FROM board WHERE id=1;
SQL;

    const INSERT_QUERY = <<<SQL
        REPLACE INTO board (
            id,
            collector_blue,
            collector_yellow,
            collector_green,
            collector_pink
        )
        VALUES (1, %s, %s, %s, %s);
SQL;

    const INSERT_GAZETTES_QUERY = <<<SQL
        REPLACE INTO gazettes (`value`, nb_diff)
        VALUES (%s, %s);
SQL;

    const SELECT_GAZETTES_QUERY = <<<SQL
        SELECT * FROM gazettes;
SQL;

    const TRUNCATE_MUSE_QUERY = <<<SQL
        TRUNCATE deck_cards;
SQL;

    const INSERT_MUSE_QUERY = <<<SQL
        INSERT INTO deck_cards (deck_number, position, muse_value, muse_color)
        VALUES (%s, %s, %s, "%s");
SQL;

    const SELECT_PLAYERS_HANDS_QUERY = <<<SQL
        SELECT id, player_id, muse_value, muse_color FROM hands;
SQL;

    const SELECT_PLAYERS_PAINTINGS_QUERY = <<<SQL
        SELECT id, player_id, muse_value, muse_color FROM paintings;
SQL;

    const TRUNCATE_PLAYERS_HANDS_QUERY = <<<SQL
        TRUNCATE hands;
SQL;

    const INSERT_PLAYERS_HANDS_QUERY = <<<SQL
        INSERT INTO hands (player_id, muse_value, muse_color)
        VALUES (%s, %s, "%s");
SQL;

    const SELECT_DECKS_QUERY = <<<SQL
        SELECT * FROM deck_cards
        WHERE deck_number = %s
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

        foreach ($board->gazettes() as $gazette) {
            ($this->table)::dbQuery(sprintf(
                self::INSERT_GAZETTES_QUERY,
                $gazette->value(),
                $gazette->nbDiff()
            ));
        }

        ($this->table)::dbQuery(self::TRUNCATE_MUSE_QUERY);
        $this->storeMuses(1, $board->decks()->firstDeck()->muses());
        $this->storeMuses(2, $board->decks()->secondDeck()->muses());
        $this->storeMuses(3, $board->decks()->thirdDeck()->muses());

        ($this->table)::dbQuery(self::TRUNCATE_PLAYERS_HANDS_QUERY);
        foreach ($board->players()->all() as $player) {
            foreach ($player->hand()->muses() as $muse) {
                ($this->table)::dbQuery(sprintf(
                    self::INSERT_PLAYERS_HANDS_QUERY,
                    $player->id(),
                    $muse->value(),
                    $muse->color()->value()
                ));
            }
        }
    }

    private function storeMuses(int $deckNumber, array $muses): void
    {
        for ($position = 0; $position < count($muses); $position++) {
            ($this->table)::dbQuery(sprintf(
                self::INSERT_MUSE_QUERY,
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

        $state['gazettes'] = $this->table->collectionFromDB(
            self::SELECT_GAZETTES_QUERY
        );

        foreach ([1, 2, 3] as $deckNumber) {
            $result = ($this->table)::dbQuery(
                sprintf(self::SELECT_DECKS_QUERY, $deckNumber)
            );

            $state['decks'][$deckNumber] = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        $state['players'] = $this->table->loadPlayersBasicInfos();
        $state['current_player'] = $this->table->currentPlayerId();
        $state['active_player'] = $this->table->activePlayerId();

        $state['hands'] = $this->table->collectionFromDB(self::SELECT_PLAYERS_HANDS_QUERY);
        $state['paintings'] = $this->table->collectionFromDB(self::SELECT_PLAYERS_PAINTINGS_QUERY);

        return Board::fromState($state);
    }
}
