<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Board;
use GBProd\Montmartre\Infrastructure\BoardRepository;

final class DrawHandler
{
    /** @var BoardRepository */
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DrawAction $action): void
    {
        $board = $this->repository->get();

        $board->draw($action->deck);

        $this->repository->save($board);
    }
}
