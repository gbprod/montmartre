<?php

use GBProd\Montmartre\Application\GetPlayerSituationHandler;
use GBProd\Montmartre\Application\StartNewGameHandler;
use GBProd\Montmartre\Infrastructure\BoardRepository;

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
]);

return $containerBuilder->build();
