<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

use GBProd\Montmartre\Domain\Event\EventRecordingCapabilities;

final class Player
{
    use EventRecordingCapabilities;

    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var Hand */
    private $hand;
    /** @var Paintings */
    private $paintings;
    /** @var Wallet */
    private $wallet;
    /** @var int */
    private $position;
    /** @var AttractedCollectors */
    private $attractedCollectors;

    private function __construct(
        int $id,
        string $name,
        int $position,
        Hand $hand,
        Paintings $paintings,
        Wallet $wallet,
        AttractedCollectors $attractedCollectors
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->hand = $hand;
        $this->paintings = $paintings;
        $this->wallet = $wallet;
        $this->position = $position;
        $this->attractedCollectors = $attractedCollectors;
    }

    public static function named(
        int $id,
        string $name,
        int $position,
        Hand $hand
    ): self {
        return new self(
            $id,
            $name,
            $position,
            $hand,
            Paintings::empty(),
            Wallet::empty(),
            AttractedCollectors::empty()
        );
    }

    public static function fromState(array $state): self
    {
        return new self(
            (int) $state['player_id'],
            $state['player_name'],
            (int) $state['player_no'] - 1, // begin at 0
            Hand::containing(
                ...array_map(
                    function ($card) {
                        $color = $card['muse_color'];

                        return Muse::painted(
                            Color::$color(),
                            (int) $card['muse_value']
                        );
                    },
                    $state['hand']
                )
            ),
            Paintings::fromMuses(
                ...array_map(
                    function ($card) {
                        $color = $card['muse_color'];

                        return Muse::painted(
                            Color::$color(),
                            (int) $card['muse_value']
                        );
                    },
                    $state['paintings']
                )
            ),
            Wallet::containing((int) $state['wallet']),
            AttractedCollectors::from(...array_map(
                function ($collector) {
                    return AttractedCollector::{$collector['color']}(
                        (int) $collector['value']
                    );
                },
                $state['attracted_collectors']
            ))
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function hand(): Hand
    {
        return $this->hand;
    }

    public function paintings(): Paintings
    {
        return $this->paintings;
    }

    public function paint(Muse ...$muses): void
    {
        foreach ($muses as $muse) {
            $this->hand = $this->hand()->withDrawed($muse);
            $this->paintings = $this->paintings->withAppended($muse);
        }
    }

    public function sellOff(Muse ...$muses): void
    {
        foreach ($muses as $muse) {
            $this->paintings = $this->paintings->withDrawed($muse);
        }

        $this->wallet = $this->wallet->withAdded(count($muses));
    }

    public function sell(Color $color): Muse
    {
        $muse = $this->paintings()
            ->maxOfColor($color);

        if (null === $muse) {
            throw new \InvalidArgumentException();
        }

        $this->paintings = $this->paintings->withDrawed($muse);

        return $muse;
    }

    public function attract(Collector $collector): void
    {
        $this->wallet = $this->wallet()
            ->withAdded($collector->willPay());
    }

    public function wallet(): Wallet
    {
        return $this->wallet;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'hand' => array_map(function (Muse $muse): array {
                return $muse->toArray();
            }, $this->hand->muses()),
            'paintings' => array_map(function (Muse $muse): array {
                return $muse->toArray();
            }, $this->paintings->muses()),
            'wallet' => $this->wallet->amount(),
        ];
    }
}
