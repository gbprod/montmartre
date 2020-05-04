<?php

namespace GBProd\Montmartre\Application;

use GBProd\Montmartre\Infrastructure\BoardRepository;

final class GetPlayerSituationHandler
{
    /** @var BoardRepository */
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetPlayerSituationQuery $query)
    {
        $board = $this->repository->get();

        return [
            'collectors' => [
                'blue' => $board->collectors()->blue()->willPay(),
                'green' => $board->collectors()->green()->willPay(),
                'yellow' => $board->collectors()->yellow()->willPay(),
                'pink' => $board->collectors()->pink()->willPay(),
            ]
        ];
    }
}
