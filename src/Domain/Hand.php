<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

use GBProd\Montmartre\Domain\Exception\HandFull;
use GBProd\Montmartre\Domain\Exception\MuseNotInHand;

final class Hand
{
    const MAX_LENGTH = 5;

    /** @var Muse[] */
    private $muses;

    private function __construct(Muse ...$muses)
    {
        if (count($muses) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException(
                "Players hand can't contains more than 5 cards"
            );
        }

        $this->muses = $muses;
    }

    public static function containing(Muse ...$muses): self
    {
        return new self(...$muses);
    }

    public function muses(): array
    {
        return $this->muses;
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
            throw new MuseNotInHand();
        }

        return Hand::containing(...$newMuses);
    }

    public function isFull(): bool
    {
        return count($this->muses) >= self::MAX_LENGTH;
    }

    public function withAppended(Muse ...$muses): self
    {
        if ($this->isFull()) {
            throw new HandFull();
        }

        if ((count($muses) + $this->count()) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException(
                "Players hand can't contains more than 5 cards"
            );
        }

        return Hand::containing(
            ...$muses,
            ...$this->muses
        );
    }

    public function count(): int
    {
        return count($this->muses);
    }
}
