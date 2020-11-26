<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Infrastructure\BoardRepository;

final class SellOffHandler
{
    /** @var BoardRepository */
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(SellOffAction $action): void
    {
        $board = $this->repository->get();

        $board->sellOff(...$action->muses);

        $this->repository->save($board);
    }
}
