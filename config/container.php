<?php

use GBProd\Montmartre\Application\GetPlayerSituationHandler;
use GBProd\Montmartre\Application\NextPlayerHandler;
use GBProd\Montmartre\Application\PaintHandler;
use GBProd\Montmartre\Application\DrawHandler;
use GBProd\Montmartre\Application\SellHandler;
use GBProd\Montmartre\Application\SellOffHandler;
use GBProd\Montmartre\Application\StartNewGameHandler;
use GBProd\Montmartre\Domain\Event\PlayerHasChanged;
use GBProd\Montmartre\Domain\Event\PlayerHasPaint;
use GBProd\Montmartre\Domain\Event\PlayerHasDrawed;
use GBProd\Montmartre\Domain\Event\PlayerHasSold;
use GBProd\Montmartre\Domain\Event\PlayerHasSoldOff;
use GBProd\Montmartre\Infrastructure\BoardRepository;
use GBProd\Montmartre\Infrastructure\EventDispatcher;
use GBProd\Montmartre\Infrastructure\Listener\NotifyWhenPlayerHasChanged;
use GBProd\Montmartre\Infrastructure\Listener\NotifyWhenPlayerHasPaint;
use GBProd\Montmartre\Infrastructure\Listener\NotifyWhenPlayerHasDrawed;
use GBProd\Montmartre\Infrastructure\Listener\NotifyWhenPlayerHasSold;
use GBProd\Montmartre\Infrastructure\Listener\NotifyWhenPlayerHasSoldOff;
use GBProd\Montmartre\Infrastructure\Listener\UpdateGameStateOnPlayerHasChanged;
use GBProd\Montmartre\Infrastructure\Listener\UpdateGameStateOnPlayerHasPaint;
use GBProd\Montmartre\Infrastructure\Listener\UpdateGameStateOnPlayerHasDrawed;
use GBProd\Montmartre\Infrastructure\Listener\UpdateGameStateOnPlayerHasSold;
use GBProd\Montmartre\Infrastructure\Listener\UpdateGameStateOnPlayerHasSoldOff;

$containerBuilder = new \DI\ContainerBuilder();

$containerBuilder->addDefinitions([
    BoardRepository::class => DI\create()->constructor(
        DI\get('table'),
        DI\get(EventDispatcher::class)
    ),

    /** Handlers **/
    StartNewGameHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    GetPlayerSituationHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    PaintHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    DrawHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    SellOffHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    SellHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    NextPlayerHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    /** Events **/
    EventDispatcher::class => DI\create()->constructor([
        PlayerHasPaint::class => [
            DI\get(NotifyWhenPlayerHasPaint::class),
            DI\get(UpdateGameStateOnPlayerHasPaint::class),
        ],
        PlayerHasSoldOff::class => [
            DI\get(NotifyWhenPlayerHasSoldOff::class),
            DI\get(UpdateGameStateOnPlayerHasSoldOff::class),
        ],
        PlayerHasChanged::class => [
            DI\get(UpdateGameStateOnPlayerHasChanged::class),
            DI\get(NotifyWhenPlayerHasChanged::class),
        ],
        PlayerHasDrawed::class => [
            DI\get(NotifyWhenPlayerHasDrawed::class),
            DI\get(UpdateGameStateOnPlayerHasDrawed::class),
        ],
        PlayerHasSold::class => [
            DI\get(NotifyWhenPlayerHasSold::class),
            DI\get(UpdateGameStateOnPlayerHasSold::class),
        ],
    ]),

    /** Notifications **/
    NotifyWhenPlayerHasPaint::class => DI\create()->constructor(
        DI\get('table')
    ),

    NotifyWhenPlayerHasSoldOff::class => DI\create()->constructor(
        DI\get('table')
    ),

    NotifyWhenPlayerHasSold::class => DI\create()->constructor(
        DI\get('table')
    ),

    NotifyWhenPlayerHasDrawed::class => DI\create()->constructor(
        DI\get('table')
    ),

    NotifyWhenPlayerHasChanged::class => DI\create()->constructor(
        DI\get('table')
    ),

    /** Update game states **/
    UpdateGameStateOnPlayerHasPaint::class => DI\create()->constructor(
        DI\get('table')
    ),

    UpdateGameStateOnPlayerHasSoldOff::class => DI\create()->constructor(
        DI\get('table')
    ),

    UpdateGameStateOnPlayerHasSold::class => DI\create()->constructor(
        DI\get('table')
    ),

    UpdateGameStateOnPlayerHasChanged::class => DI\create()->constructor(
        DI\get('table')
    ),

    UpdateGameStateOnPlayerHasDrawed::class => DI\create()->constructor(
        DI\get('table')
    ),
]);

return $containerBuilder->build();
