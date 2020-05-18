<?php

namespace GBProd\Montmartre\Domain;

final class Player
{
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
}
