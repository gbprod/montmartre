<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure;

use GBProd\Montmartre\Domain\Board;
use GBProd\Montmartre\Domain\Event\BoardHasBeenSetUp;
use GBProd\Montmartre\Domain\Event\PlayerHasPaint;

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
        INSERT INTO gazettes (`value`, nb_diff)
        VALUES (%s, %s);
SQL;

    const SELECT_GAZETTES_QUERY = <<<SQL
        SELECT id, `value`, nb_diff FROM gazettes;
SQL;

    const INSERT_MUSE_QUERY = <<<SQL
        INSERT INTO deck_cards (deck_number, position, muse_value, muse_color)
        VALUES (%s, %s, %s, "%s");
SQL;

    const SELECT_PLAYER_HANDS_QUERY = <<<SQL
        SELECT id, player_id, muse_value, muse_color
        FROM hands
        WHERE player_id = %s;
SQL;

    const SELECT_FIRST_MUSE_FROM_PLAYER_HAND_QUERY = <<<SQL
        SELECT id, player_id, muse_value, muse_color
        FROM hands
        WHERE player_id = %s
          AND muse_value = %s
          AND muse_color = "%s"
        LIMIT 1;
SQL;
    const INSERT_PAINTING_QUERY = <<<SQL
        INSERT INTO paintings (player_id, muse_value, muse_color)
        VALUES (%s, %s, "%s");
SQL;

    const DELETE_MUSE_FROM_PLAYER_HAND_QUERY = <<<SQL
        DELETE FROM hands WHERE id = %s;
SQL;

    const SELECT_PLAYER_PAINTINGS_QUERY = <<<SQL
        SELECT id, player_id, muse_value, muse_color
        FROM paintings
        WHERE player_id = %s;
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

    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function get(): Board
    {
        $state = $this->table->objectFromDB(self::SELECT_QUERY);

        $state['gazettes'] = $this->table->collectionFromDB(self::SELECT_GAZETTES_QUERY);

        foreach ([1, 2, 3] as $deckNumber) {
            $state['decks'][$deckNumber] = $this->table->collectionFromDB(
                sprintf(self::SELECT_DECKS_QUERY, $deckNumber)
            );
        }

        $state['current_player'] = $this->table->currentPlayerId();
        $state['active_player'] = $this->table->activePlayerId();

        $players = $this->table->loadPlayersBasicInfos();
        $state['players'] = array_map(
            function ($player, $playerId) {
                $player['hand'] = $this->table->collectionFromDB(
                    sprintf(self::SELECT_PLAYER_HANDS_QUERY, $playerId)
                );

                $player['paintings'] = $this->table->collectionFromDB(
                    sprintf(self::SELECT_PLAYER_PAINTINGS_QUERY, $playerId)
                );

                return $player;
            },
            $players,
            array_keys($players)
        );

        return Board::fromState($state);
    }

    public function save(Board $board): void
    {
        $events = $board->releaseEvents();

        foreach ($events as $event) {
            $class = substr(get_class($event), strrpos(get_class($event), '\\') + 1);
            $method = sprintf('apply%s', $class);
            if (!method_exists($this, $method)) {
                throw new \RuntimeException(
                    sprintf('Method %s doesn\'t exist', $method)
                );
            }
            $this->$method($event, $board);
        }
    }

    private function applyBoardHasBeenSetUp(BoardHasBeenSetUp $event, Board $board): void
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

        $this->storeMuses(1, $board->decks()->firstDeck()->muses());
        $this->storeMuses(2, $board->decks()->secondDeck()->muses());
        $this->storeMuses(3, $board->decks()->thirdDeck()->muses());

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

    private function applyPlayerHasPaint(PlayerHasPaint $event, Board $board)
    {
        foreach ($event->muses as $muse) {
            $museState = $this->table->objectFromDB(
                sprintf(
                    self::SELECT_FIRST_MUSE_FROM_PLAYER_HAND_QUERY,
                    $event->playerId,
                    $muse->value(),
                    $muse->color()->value()
                )
            );

            if (null === $museState) {
                throw new \RuntimeException("Muse not found in hand, should not happens");
            }

            ($this->table)::dbQuery(
                sprintf(self::DELETE_MUSE_FROM_PLAYER_HAND_QUERY, $museState['id'])
            );

            ($this->table)::dbQuery(
                sprintf(
                    self::INSERT_PAINTING_QUERY,
                    $event->playerId,
                    $muse->value(),
                    $muse->color()->value()
                )
            );
        }
    }
}
