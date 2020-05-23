<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Infrastructure\BoardRepository;

final class PaintHandler
{
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(PaintAction $action): void
    {
        $board = $this->repository->get();

        $board->paint(...$action->muses);

        $this->repository->save($board);
    }
}
