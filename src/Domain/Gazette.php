<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Domain;

final class Gazette
{
    /** @var int */
    private $nbDiff;
    /** @var int */
    private $value;

    private function __construct(int $nbDiff, int $value)
    {
        $this->nbDiff = $nbDiff;
        $this->value = $value;
    }

    public static function forPublishing(int $nbDiff, int $value): self
    {
        return new self($nbDiff, $value);
    }

    public function nbDiff(): int
    {
        return $this->nbDiff;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'nbDiff' => $this->nbDiff,
            'value' => $this->value,
        ];
    }
}
