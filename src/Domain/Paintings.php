<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Paintings
{
    private const MAX_PAINTINGS = 6;

    private $muses;

    private function __construct(Muse ...$muses)
    {
        $this->muses = $muses;
    }

    public static function fromMuses(Muse ...$muses): self
    {
        return new self(...$muses);
    }

    public static function empty(): self
    {
        return new self();
    }

    public function muses(): array
    {
        return $this->muses;
    }

    public function withAppended(Muse $muse): self
    {
        $self = clone $this;

        $self->muses[] = $muse;

        return $self;
    }
}
