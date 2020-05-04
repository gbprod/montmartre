<?php

namespace GBProd\Montmartre\Application;

final class GetPlayerSituationQuery
{
    /** @var int */
    public $playerId;

    public static function byId(int $id)
    {
        $self = new self();
        $self->playerId = $id;

        return $self;
    }
}
