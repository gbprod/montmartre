<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

use GBProd\Montmartre\Domain\Event\EventRecordingCapabilities;
use GBProd\Montmartre\Domain\Exception\ShouldPaintAtLeastOneMuse;
use GBProd\Montmartre\Domain\Exception\CantPaint2MusesIfSumMoreThan5;
use GBProd\Montmartre\Domain\Exception\CantPaintMoreThan2Muses;

final class Player
{
    use EventRecordingCapabilities;

    private $id;
    private $name;
    private $hand;
    private $paintings;

    private function __construct(
        int $id,
        string $name,
        Hand $hand,
        Paintings $paintings
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->hand = $hand;
        $this->paintings = $paintings;
    }

    public static function named(
        int $id,
        string $name,
        Hand $hand,
        Paintings $paintings
    ): self {
        return new self($id, $name, $hand, $paintings);
    }

    public static function fromState(array $state)
    {
        return new self(
            (int) $state['player_id'],
            $state['player_name'],
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
        if (count($muses) <= 0) {
            throw new ShouldPaintAtLeastOneMuse();
        }

        if (count($muses) > 2) {
            throw new CantPaintMoreThan2Muses();
        }

        if (count($muses) === 2 && ($muses[0]->value() + $muses[1]->value()) > 5) {
            throw new CantPaint2MusesIfSumMoreThan5();
        }

        foreach ($muses as $muse) {
            $this->hand = $this->hand()->withDrawed($muse);
            $this->paintings = $this->paintings->withAppended($muse);
        }
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
        ];
    }
}
