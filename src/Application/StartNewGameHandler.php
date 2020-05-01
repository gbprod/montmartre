<?php

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Board;
use GBProd\Montmartre\Infrastructure\BoardRepository;

final class StartNewGameHandler
{
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke()
    {
        $board = Board::initialize();

        $this->repository->save($board);
    }
}
