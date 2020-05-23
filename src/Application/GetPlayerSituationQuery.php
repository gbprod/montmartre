<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

final class GetPlayerSituationQuery
{
    /** @var int */
    public $playerId;

    public static function byId(int $id): self
    {
        $self = new self();
        $self->playerId = $id;

        return $self;
    }
}
