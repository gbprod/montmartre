<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

use GBProd\Montmartre\Domain\Event\BoardHasBeenSetUp;
use GBProd\Montmartre\Domain\Event\EventRecordingCapabilities;
use GBProd\Montmartre\Domain\Event\PlayerHasChanged;
use GBProd\Montmartre\Domain\Event\PlayerHasPaint;
use GBProd\Montmartre\Domain\Event\PlayerHasPicked;
use GBProd\Montmartre\Domain\Event\PlayerHasSold;
use GBProd\Montmartre\Domain\Event\PlayerHasSoldOff;
use GBProd\Montmartre\Domain\Exception\CantPaint2MusesIfSumMoreThan5;
use GBProd\Montmartre\Domain\Exception\CantPaintMoreThan2Muses;
use GBProd\Montmartre\Domain\Exception\HandFull;
use GBProd\Montmartre\Domain\Exception\NoCollectorLeft;
use GBProd\Montmartre\Domain\Exception\ShouldHaveMajority;
use GBProd\Montmartre\Domain\Exception\ShouldPaintAtLeastOneMuse;
use GBProd\Montmartre\Domain\Exception\ShouldSellOffAtLeastOneMuse;
use GBProd\Montmartre\Domain\Services\ResolvePlayerMajorities;

final class Board
{
    use EventRecordingCapabilities;

    /** @var Collectors */
    private $collectors;
    /** @var Gazettes */
    private $gazettes;
    /** @var Decks */
    private $decks;
    /** @var Players */
    private $players;

    private function __construct(
        Collectors $collectors,
        Gazettes $gazettes,
        Decks $decks,
        Players $players
    ) {
        $this->collectors = $collectors;
        $this->gazettes = $gazettes;
        $this->decks = $decks;
        $this->players = $players;
    }

    public static function setup(array $players): self
    {
        $initialDeck = Deck::full();

        $players = Players::place(
            ...array_map(
                function ($playerState, $playerId) use ($initialDeck) {
                    return Player::named(
                        $playerId,
                        $playerState['player_name'],
                        ((int) $playerState['player_table_order']) - 1,
                        Hand::containing(...$initialDeck->pick(5)),
                        Paintings::empty(),
                        Wallet::empty()
                    );
                },
                $players,
                array_keys($players)
            )
        );

        $self = new self(
            Collectors::distribute(),
            Gazettes::distribute(),
            Decks::distribute($initialDeck),
            $players
        );


        $self->recordThat(new BoardHasBeenSetUp());

        return $self;
    }

    public static function fromState(array $state): self
    {
        return new self(
            Collectors::paying(
                (int) $state['collector_blue'],
                (int) $state['collector_yellow'],
                (int) $state['collector_green'],
                (int) $state['collector_pink']
            ),
            Gazettes::fromRemaining(
                ...array_map(static function ($gazetteState) {
                    return Gazette::forPublishing(
                        (int) $gazetteState['nb_diff'],
                        (int) $gazetteState['value']
                    );
                }, $state['gazettes'])
            ),
            Decks::fromRemaining(
                ...array_map(function ($number) use ($state): Deck {
                    return Deck::fromRemaining(
                        ...array_map(function ($museState): Muse {
                            $color = $museState['muse_color'];

                            return Muse::painted(
                                Color::$color(),
                                (int) $museState['muse_value']
                            );
                        }, $state['decks'][$number])
                    );
                }, [1, 2, 3])
            ),
            Players::from(
                ...array_reduce(
                    $state['players'],
                    function ($carry, $item) use ($state) {
                        if ($item['player_id'] == $state['current_player']) {
                            $carry[0] = Player::fromState($item);
                        }

                        if ($item['player_id'] == $state['active_player']) {
                            $carry[1] = Player::fromState($item);
                        }

                        if ($item['player_id'] == $state['next_player']) {
                            $carry[2] = Player::fromState($item);
                        }

                        if ($item['player_id'] != $state['current_player']) {
                            $carry[3][] = Player::fromState($item);
                        }

                        return $carry;
                    },
                    [null, null, []]
                )
            )
        );
    }

    public function collectors(): Collectors
    {
        return $this->collectors;
    }

    public function gazettes(): Gazettes
    {
        return $this->gazettes;
    }

    public function decks(): Decks
    {
        return $this->decks;
    }

    public function players(): Players
    {
        return $this->players;
    }

    public function paint(Muse ...$muses): void
    {
        if (count($muses) <= 0) {
            throw new ShouldPaintAtLeastOneMuse();
        }

        if (count($muses) > 2) {
            throw new CantPaintMoreThan2Muses();
        }

        if (count($muses) === 2 && ($muses[0]->value() + $muses[1]->value()) > 5) {
            throw new CantPaint2MusesIfSumMoreThan5();
        }

        $this->players()
            ->current()
            ->paint(...$muses);

        $this->recordThat(new PlayerHasPaint(
            $this->players()->current(),
            ...$muses
        ));
    }

    public function sellOff(Muse ...$muses): void
    {
        if (count($muses) <= 0) {
            throw new ShouldSellOffAtLeastOneMuse();
        }

        $this->players()
            ->current()
            ->sellOff(...$muses);

        $this->recordThat(new PlayerHasSoldOff(
            $this->players()->current(),
            ...$muses
        ));
    }

    public function pick(int $deckNumber): void
    {
        $player = $this->players()->current();
        if ($player->hand()->isFull()) {
            throw new HandFull();
        }

        $deck = $this->decks()->byNumber($deckNumber);

        if ($deck->isEmpty()) {
            throw new EmptyDeck();
        }

        $picked = $deck->pick(
            Hand::MAX_LENGTH - $player->hand()->count()
        );

        $player->hand()->withAppended(...$picked);

        $this->recordThat(
            new PlayerHasPicked(
                $this->players()->current(),
                $deck,
                $deckNumber,
                ...$picked
            )
        );
    }

    public function nextPlayer(): void
    {
        $this->players = $this->players->toNext();

        $this->recordThat(
            new PlayerHasChanged($this->players)
        );
    }

    public function sell(Color $color): void
    {
        $majorities = ResolvePlayerMajorities::resolve(
            $this->players()
        );

        if (!in_array($color, $majorities)) {
            throw new ShouldHaveMajority();
        }

        $collector = $this->collectors()
            ->pick($color);

        if (null === $collector) {
            throw new NoCollectorLeft($color);
        }
        $muse = $this->players()->current()
            ->sell($color);


        $this->players()->current()
            ->attract($collector);

        $this->recordThat(
            new PlayerHasSold(
                $this->players->current(),
                $muse,
                $collector,
                $this->collectors()->{$color->value()}()
            )
        );
    }
}
