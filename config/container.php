<?php

use GBProd\Montmartre\Application\GetPlayerSituationHandler;
use GBProd\Montmartre\Application\NextPlayerHandler;
use GBProd\Montmartre\Application\PaintHandler;
use GBProd\Montmartre\Application\PickHandler;
use GBProd\Montmartre\Application\StartNewGameHandler;
use GBProd\Montmartre\Domain\Event\PlayerHasChanged;
use GBProd\Montmartre\Domain\Event\PlayerHasPaint;
use GBProd\Montmartre\Domain\Event\PlayerHasPicked;
use GBProd\Montmartre\Domain\Event\PlayerHasSoldOff;
use GBProd\Montmartre\Infrastructure\BoardRepository;
use GBProd\Montmartre\Infrastructure\EventDispatcher;
use GBProd\Montmartre\Infrastructure\Listener\NotifyWhenPlayerHasPaint;
use GBProd\Montmartre\Infrastructure\Listener\NotifyWhenPlayerHasPicked;
use GBProd\Montmartre\Infrastructure\Listener\NotifyWhenPlayerHasSoldOff;
use GBProd\Montmartre\Infrastructure\Listener\UpdateGameStateOnPlayerHasChanged;
use GBProd\Montmartre\Infrastructure\Listener\UpdateGameStateOnPlayerHasPaint;
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

    PickHandler::class => DI\create()->constructor(
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
        ],
        PlayerHasPicked::class => [
            DI\get(NotifyWhenPlayerHasPicked::class)
        ],
    ]),

    /** Notifications **/
    NotifyWhenPlayerHasPaint::class => DI\create()->constructor(
        DI\get('table')
    ),

    NotifyWhenPlayerHasSoldOff::class => DI\create()->constructor(
        DI\get('table')
    ),

    NotifyWhenPlayerHasPicked::class => DI\create()->constructor(
        DI\get('table')
    ),

    /** Update game states **/
    UpdateGameStateOnPlayerHasPaint::class => DI\create()->constructor(
        DI\get('table')
    ),

    UpdateGameStateOnPlayerHasSoldOff::class => DI\create()->constructor(
        DI\get('table')
    ),

    UpdateGameStateOnPlayerHasChanged::class => DI\create()->constructor(
        DI\get('table')
    ),
]);

return $containerBuilder->build();
