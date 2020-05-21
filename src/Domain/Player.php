<?php

namespace GBProd\Montmartre\Domain;

use GBProd\Montmartre\Domain\Event\EventRecordingCapabilities;

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

        // $this->recordThat(PlayerHasPaint::from($this, ...$muses));
    }
}
