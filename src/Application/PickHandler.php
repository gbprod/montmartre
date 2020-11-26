<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Board;
use GBProd\Montmartre\Infrastructure\BoardRepository;

final class PickHandler
{
    /** @var BoardRepository */
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(PickAction $action): void
    {
        $board = $this->repository->get();

        $board->pick($action->deck);

        $this->repository->save($board);
    }
}
