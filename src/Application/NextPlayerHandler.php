<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Infrastructure\BoardRepository;

final class NextPlayerHandler
{
    /** @var BoardRepository */
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(): void
    {
        $board = $this->repository->get();

        $board->nextPlayer();

        $this->repository->save($board);
    }
}
