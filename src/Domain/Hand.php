<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Hand
{
    private $muses;

    private function __construct(Muse ...$muses)
    {
        if (count($muses) > 5) {
            throw new \InvalidArgumentException("Players hand can't contains more than 5 cards");
        }

        $this->muses = $muses;
    }

    public static function containing(Muse ...$muses)
    {
        return new self(...$muses);
    }

    public function muses(): array
    {
        return $this->muses;
    }

    public function withDrawed(Muse $muse): self
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
}
