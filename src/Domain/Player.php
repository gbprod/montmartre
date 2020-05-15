<?php

namespace GBProd\Montmartre\Domain;

final class Player
{
    private $id;
    private $name;

    private function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function named(int $id, string $name): self
    {
        return new self($id, $name);
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
