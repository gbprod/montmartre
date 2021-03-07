<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Board;
use GBProd\Montmartre\Infrastructure\BoardRepository;

final class BuyGazetteHandler
{
    /** @var BoardRepository */
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(BuyGazetteAction $action): void
    {
        $board = $this->repository->get();

        $board->buyGazette($action->nbDiff);

        $this->repository->save($board);
    }
}
