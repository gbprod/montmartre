<?php

namespace GBProd\Montmartre\Domain;

final class Player
{
    private $id;
    private $name;
    private $hand;

    private function __construct(int $id, string $name, Hand $hand)
    {
        $this->id = $id;
        $this->name = $name;
        $this->hand = $hand;
    }

    public static function named(int $id, string $name, Hand $hand): self
    {
        return new self($id, $name, $hand);
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
}
