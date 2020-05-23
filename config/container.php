<?php

use GBProd\Montmartre\Application\GetPlayerSituationHandler;
use GBProd\Montmartre\Application\PaintHandler;
use GBProd\Montmartre\Application\StartNewGameHandler;
use GBProd\Montmartre\Infrastructure\BoardRepository;
use GBProd\Montmartre\Infrastructure\EventDispatcher;

$containerBuilder = new \DI\ContainerBuilder();

$containerBuilder->addDefinitions([
    StartNewGameHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    BoardRepository::class => DI\create()->constructor(
        DI\get('table')
    ),

    GetPlayerSituationHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    PaintHandler::class => DI\create()->constructor(
        DI\get(BoardRepository::class)
    ),

    EventDispatcher::class => DI\create()->constructor([]),
]);

return $containerBuilder->build();
