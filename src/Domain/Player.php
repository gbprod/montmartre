<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

use GBProd\Montmartre\Domain\Event\EventRecordingCapabilities;

final class Player
{
    use EventRecordingCapabilities;

    private $id;
    private $name;
    /** @var Hand */
    private $hand;
    /** @var Paintings */
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
        foreach ($muses as $muse) {
            $this->hand = $this->hand()->withPicked($muse);
            $this->paintings = $this->paintings->withAppended($muse);
        }
    }

    public function sellOff(Muse ...$muses): void
    {
        foreach ($muses as $muse) {
            $this->paintings = $this->paintings->withPicked($muse);
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
