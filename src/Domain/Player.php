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
    /** @var bool */
    private $allowedToBuyGazette = false;
    /** @var ?Gazette */
    private $gazette;

    private function __construct(
        int $id,
        string $name,
        int $position,
        Hand $hand,
        Paintings $paintings,
        Wallet $wallet,
        AttractedCollectors $attractedCollectors,
        bool $allowedToBuyGazette,
        ?Gazette $gazette
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->hand = $hand;
        $this->paintings = $paintings;
        $this->wallet = $wallet;
        $this->position = $position;
        $this->attractedCollectors = $attractedCollectors;
        $this->allowedToBuyGazette = $allowedToBuyGazette;
        $this->gazette = $gazette;
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
            AttractedCollectors::empty(),
            false,
            null
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
                    return Collector::{$collector['color']}(
                        (int) $collector['value']
                    );
                },
                $state['attracted_collectors']
            )),
            (bool) $state['can_buy_gazette'],
            Gazette::forPublishing(
                (int) $state['gazette_nb_diff'],
                (int) $state['gazette_value']
            )
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
        $previousCount = $this->attractedCollectors->countDisctinctColors();

        $this->attractedCollectors = AttractedCollectors::from(
            $collector,
            ...iterator_to_array($this->attractedCollectors)
        );

        if ($this->attractedCollectors->countDisctinctColors() > $previousCount) {
            $this->allowedToBuyGazette = true;
        }
    }

    public function wallet(): Wallet
    {
        return $this->wallet;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function draw(Muse ...$muses): void
    {
        $this->hand = $this->hand->withAppended(...$muses);
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

    public function attractedCollectors(): AttractedCollectors
    {
        return $this->attractedCollectors;
    }

    public function allowedToBuyGazette(): bool
    {
        return null === $this->gazette && $this->allowedToBuyGazette;
    }

    public function finishTurn(): void
    {
        $this->allowedToBuyGazette = false;
    }

    public function buyGazette(Gazette $gazette): void
    {
        $this->gazette = $gazette;
    }

    public function gazette(): ?Gazette
    {
        return $this->gazette;
    }
}
