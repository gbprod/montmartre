<?php

use GBProd\Montmartre\Application\GetPlayerSituationHandler;
use GBProd\Montmartre\Application\PaintHandler;
use GBProd\Montmartre\Application\StartNewGameHandler;
use GBProd\Montmartre\Domain\Event\PlayerHasPaint;
use GBProd\Montmartre\Infrastructure\BoardRepository;
use GBProd\Montmartre\Infrastructure\EventDispatcher;
use GBProd\Montmartre\Infrastructure\Listener\NotifyWhenPlayerHasPaint;
use GBProd\Montmartre\Infrastructure\Listener\UpdateGameStateOnPlayerHasPaint;

$containerBuilder = new \DI\ContainerBuilder();

$containerBuilder->addDefinitions([
    StartNewGameHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    BoardRepository::class => DI\create()->constructor(
        DI\get('table'),
        DI\get(EventDispatcher::class)
    ),

    GetPlayerSituationHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    PaintHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    EventDispatcher::class => DI\create()->constructor([
        PlayerHasPaint::class => [
            DI\get(NotifyWhenPlayerHasPaint::class),
            DI\get(UpdateGameStateOnPlayerHasPaint::class),
        ],
    ]),

    NotifyWhenPlayerHasPaint::class => DI\create()->constructor(
        DI\get('table')
    ),

    UpdateGameStateOnPlayerHasPaint::class => DI\create()->constructor(
        DI\get('table')
    ),
]);

return $containerBuilder->build();
