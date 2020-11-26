<?php

declare(strict_types=1);

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Domain\Color;
use GBProd\Montmartre\Infrastructure\BoardRepository;

final class SellHandler
{
    /** @var BoardRepository */
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(SellAction $action): void
    {
        $board = $this->repository->get();

        $board->sell(
            Color::fromString($action->color)
        );

        $this->repository->save($board);
    }
}
