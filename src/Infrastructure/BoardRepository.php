<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Infrastructure;

use GBProd\Montmartre\Domain\Board;
use GBProd\Montmartre\Domain\Event\BoardHasBeenSetUp;
use GBProd\Montmartre\Domain\Event\MusesHasBeenDiscarded;
use GBProd\Montmartre\Domain\Event\PlayerHasChanged;
use GBProd\Montmartre\Domain\Event\PlayerHasDrawed;
use GBProd\Montmartre\Domain\Event\PlayerHasPaint;
use GBProd\Montmartre\Domain\Event\PlayerHasSold;
use GBProd\Montmartre\Domain\Event\PlayerHasSoldOff;
use GBProd\Montmartre\Domain\Muse;

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
            collector_pink,
            ambroise
        )
        VALUES (1, %s, %s, %s, %s, %s);
SQL;

    const UPDATE_COLLECTOR_QUERY = <<<SQL
        UPDATE board
        SET collector_%s = "%s"
        WHERE id=1
SQL;

    const UPDATE_AMBROISE_QUERY = <<<SQL
        UPDATE board
        SET ambroise = "%s"
        WHERE id=1
SQL;

    const SELECT_DISCARD_PILE_QUERY = <<<SQL
        SELECT id, muse_value, muse_color
        FROM `discard_pile`;
SQL;

    const INSERT_DISCARD_PILE_QUERY = <<<SQL
        INSERT INTO `discard_pile` (`muse_value`, `muse_color`)
        VALUES (%s, "%s");
SQL;

    const SELECT_PLAYERS_QUERY = <<<SQL
        SELECT player_id, player_name, player_no, player_score
        FROM player;
SQL;

    const INSERT_GAZETTES_QUERY = <<<SQL
        INSERT INTO gazettes (`value`, nb_diff)
        VALUES (%s, %s);
SQL;

    const SELECT_GAZETTES_QUERY = <<<SQL
        SELECT id, `value`, nb_diff FROM gazettes;
SQL;

    const DROP_DECK = <<<SQL
        DELETE FROM deck_cards
        WHERE deck_number = %s;
SQL;

    const INSERT_DECK_MUSE_QUERY = <<<SQL
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

    const SELECT_FIRST_MUSE_FROM_PLAYER_PAINTINGS_QUERY = <<<SQL
        SELECT *
        FROM paintings
        WHERE player_id = %s
          AND muse_value = %s
          AND muse_color = "%s"
        LIMIT 1;
SQL;

    const DELETE_MUSE_FROM_PAINTINGS_QUERY = <<<SQL
        DELETE FROM paintings WHERE id = %s;
SQL;

    const UPDATE_SCORE_QUERY = <<<SQL
        UPDATE player
        SET player_score = %s
        WHERE player_id = %s;
SQL;

    const INSERT_ATTRACTED_COLLECTOR = <<<SQL
        INSERT INTO `attracted_collector` (player_id, value, color)
        VALUES (%s, %s, '%s');
SQL;

    const SELECT_PLAYER_ATTRACTED_COLLECTORS_QUERY = <<<SQL
        SELECT * FROM `attracted_collector`
        WHERE player_id = %s;
SQL;

    private $table;
    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct($table, EventDispatcher $eventDispatcher)
    {
        $this->table = $table;
        $this->eventDispatcher = $eventDispatcher;
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
        $state['next_player'] = $this->table->getPlayerAfter($state['active_player']);

        $players = $this->table->collectionFromDB(self::SELECT_PLAYERS_QUERY);

        $state['players'] = array_map(
            function ($player, $playerId) {
                $player['hand'] = $this->table->collectionFromDB(
                    sprintf(self::SELECT_PLAYER_HANDS_QUERY, $playerId)
                );

                $player['paintings'] = $this->table->collectionFromDB(
                    sprintf(self::SELECT_PLAYER_PAINTINGS_QUERY, $playerId)
                );

                $player['attracted_collectors'] = $this->table->collectionFromDB(
                    sprintf(self::SELECT_PLAYER_ATTRACTED_COLLECTORS_QUERY, $playerId)
                );

                $player['wallet'] = $player['player_score'];

                return $player;
            },
            $players,
            array_keys($players)
        );

        $state['discard_pile'] = $this->table->collectionFromDB(
            self::SELECT_DISCARD_PILE_QUERY
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

        $this->eventDispatcher->dispatch($events);
    }

    private function applyBoardHasBeenSetUp(BoardHasBeenSetUp $event, Board $board): void
    {
        ($this->table)::dbQuery(sprintf(
            self::INSERT_QUERY,
            null !== $board->collectors()->blue() ? $board->collectors()->blue()->willPay() : 'null',
            null !== $board->collectors()->yellow() ? $board->collectors()->yellow()->willPay() : 'null',
            null !== $board->collectors()->green() ? $board->collectors()->green()->willPay() : 'null',
            null !== $board->collectors()->pink() ? $board->collectors()->pink()->willPay() : 'null',
            null !== $board->ambroise()->color() ? $board->ambroise()->color()->value() : 'null'
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
        for ($position = 0; $position < count($muses); ++$position) {
            ($this->table)::dbQuery(sprintf(
                self::INSERT_DECK_MUSE_QUERY,
                $deckNumber,
                $position,
                $muses[$position]->value(),
                $muses[$position]->color()->value()
            ));
        }
    }

    private function applyPlayerHasPaint(PlayerHasPaint $event, Board $board): void
    {
        foreach ($event->muses() as $muse) {
            $museState = $this->table->objectFromDB(
                sprintf(
                    self::SELECT_FIRST_MUSE_FROM_PLAYER_HAND_QUERY,
                    $event->player()->id(),
                    $muse->value(),
                    $muse->color()->value()
                )
            );

            if (null === $museState) {
                throw new \RuntimeException('Muse not found in hand, should not happens');
            }

            ($this->table)::dbQuery(
                sprintf(self::DELETE_MUSE_FROM_PLAYER_HAND_QUERY, $museState['id'])
            );

            ($this->table)::dbQuery(
                sprintf(
                    self::INSERT_PAINTING_QUERY,
                    $event->player()->id(),
                    $muse->value(),
                    $muse->color()->value()
                )
            );
        }
    }

    private function applyPlayerHasSoldOff(PlayerHasSoldOff $event, Board $board): void
    {
        foreach ($event->muses() as $muse) {
            $museState = $this->table->objectFromDB(
                sprintf(
                    self::SELECT_FIRST_MUSE_FROM_PLAYER_PAINTINGS_QUERY,
                    $event->player()->id(),
                    $muse->value(),
                    $muse->color()->value()
                )
            );

            if (null === $museState) {
                throw new \RuntimeException('Muse not found in hand, should not happens');
            }

            ($this->table)::dbQuery(
                sprintf(self::DELETE_MUSE_FROM_PAINTINGS_QUERY, $museState['id'])
            );
        }

        ($this->table)::dbQuery(
            sprintf(
                self::UPDATE_SCORE_QUERY,
                $event->player()->wallet()->amount(),
                $event->player()->id()
            )
        );
    }

    private function applyPlayerHasChanged(PlayerHasChanged $event, Board $board): void
    {
    }

    private function applyPlayerHasDrawed(PlayerHasDrawed $event, Board $board): void
    {
        foreach ($event->muses() as $muse) {
            ($this->table)::dbQuery(
                sprintf(
                    self::INSERT_PLAYERS_HANDS_QUERY,
                    $event->player()->id(),
                    $muse->value(),
                    $muse->color()->value()
                )
            );
        }

        ($this->table)::dbQuery(
            sprintf(
                self::DROP_DECK,
                $event->deckNumber()
            )
        );

        $this->storeMuses(
            $event->deckNumber(),
            $board->decks()->byNumber($event->deckNumber())->muses()
        );
    }

    private function applyPlayerHasSold(PlayerHasSold $event, Board $board): void
    {
        $museState = $this->table->objectFromDB(
            sprintf(
                self::SELECT_FIRST_MUSE_FROM_PLAYER_PAINTINGS_QUERY,
                $event->player()->id(),
                $event->muse()->value(),
                $event->muse()->color()->value()
            )
        );

        if (null === $museState) {
            throw new \RuntimeException('Muse not found in hand, should not happens');
        }

        ($this->table)::dbQuery(
            sprintf(self::DELETE_MUSE_FROM_PAINTINGS_QUERY, $museState['id'])
        );

        ($this->table)::dbQuery(
            sprintf(
                self::UPDATE_SCORE_QUERY,
                $event->player()->wallet()->amount(),
                $event->player()->id()
            )
        );

        ($this->table)::dbQuery(
            sprintf(
                self::INSERT_ATTRACTED_COLLECTOR,
                $event->player()->id(),
                $event->attractedCollector()->willPay(),
                $event->muse()->color()->value()
            )
        );

        ($this->table)::dbQuery(
            sprintf(
                self::UPDATE_AMBROISE_QUERY,
                $event->muse()->color()->value()
            )
        );
    }

    private function applyMusesHasBeenDiscarded(MusesHasBeenDiscarded $event, Board $board): void
    {
        /** @var Muse $muse */
        foreach ($event->muses() as $muse) {
            ($this->table)::dbQuery(
                sprintf(
                    self::INSERT_DISCARD_PILE_QUERY,
                    $muse->value(),
                    $muse->color()->value()
                )
            );
        }
    }
}
