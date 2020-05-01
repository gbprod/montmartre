<?php

namespace GBProd\Montmartre\Domain;

final class Player
{
    private $id;
    private $name;

    private function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function fromState(array $state)
    {
        $self = new self(
            $state['id'],
            $state['name']
        );
    }

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        return $this->name;
    }
}
