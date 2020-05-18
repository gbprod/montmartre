<?php

namespace GBProd\Montmartre\Domain;

final class Paintings
{
    private $muses;

    private function __construct(Muse ...$muses)
    {
        $this->muses = $muses;
    }

    public static function fromMuses(Muse ...$muses)
    {
        return new self(...$muses);
    }

    public static function empty()
    {
        return new self();
    }

    public function muses()
    {
        return $this->muses;
    }
}
