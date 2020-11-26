<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

use GBProd\Montmartre\Domain\Exception\MuseNotPainted;

final class Paintings
{
    private const MAX_PAINTINGS = 6;

    /** @var Muse[] */
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

    public function maxOfColor(Color $color): ?Muse
    {
        return array_reduce(
            $this->muses,
            function (?Muse $carry, Muse $muse) use ($color): ?Muse {
                if (!$muse->color()->equals($color)) {
                    return $carry;
                }

                if (null === $carry || $muse->value() > $carry->value()) {
                    return $muse;
                }

                return $carry;
            },
            null
        );
    }

    public function withPicked(Muse $muse): self
    {
        $found = false;
        $newMuses = [];
        for ($index = 0; $index < count($this->muses); $index++) {
            if (!$found && $muse->equals($this->muses[$index])) {
                $found = true;
            } else {
                $newMuses[] = $this->muses[$index];
            }
        }

        if (!$found) {
            throw new MuseNotPainted();
        }

        return Paintings::fromMuses(...$newMuses);
    }
}
