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
    /** @var Wallet */
    private $wallet;

    private function __construct(
        int $id,
        string $name,
        Hand $hand,
        Paintings $paintings,
        Wallet $wallet
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->hand = $hand;
        $this->paintings = $paintings;
        $this->wallet = $wallet;
    }

    public static function named(
        int $id,
        string $name,
        Hand $hand,
        Paintings $paintings,
        Wallet $wallet
    ): self {
        return new self($id, $name, $hand, $paintings, $wallet);
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
            ),
            Wallet::containing((int) $state['wallet'])
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

        $this->wallet = $this->wallet->withAdded(count($muses));
    }

    public function wallet(): Wallet
    {
        return $this->wallet;
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
